<?php

namespace App\Repositories;

use App\Models\DocumentBody;
use App\Models\DocumentHeader;
use App\Models\Version;
use App\Services\DocumentServiceImpl;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentRepositoryImp implements DocumentRepository
{
  
    public function storeOrUpdateDocumentKey(array $data, $id = null)
    {
        DB::beginTransaction();
    
        try {
            // Ensure the user is authenticated before proceeding
            $user = Auth::user();
            if (!$user) {
                throw new Exception('User not authenticated'); // Unauthorized
            }
    
            // Fetch or create a new DocumentHeader
            $documentHeader = $id ? DocumentHeader::findOrFail($id) : new DocumentHeader();
    
            // Step 1: Generate and encrypt the encryption key
            $encryptionKey = $this->generateEncryptionKey();
            $encryptedKey = $this->encryptKey($encryptionKey);
    
            // Step 2: Generate IV and encrypt header & body
            $iv = $this->generateIV();
            [$encryptedHeader, $encryptedBody] = $this->encryptDocument($data['header'], $data['body'], $encryptionKey, $iv);
    
            // Step 3: Generate checksum for the encrypted body
            $checksum = $this->generateChecksum($encryptedBody);
    
            if ($id) {
                // If updating, log the audit and create a version
                $changesSummary = DocumentServiceImpl::generateChangesSummary($documentHeader, $data);
                DocumentServiceImpl::logAudit($user->id, $documentHeader->id, $changesSummary, 'update');
                $this->createVersion($id, $changesSummary);
            }
    
            // Step 4: Save the document header (either insert or update)
            $headerDocument = $this->saveDocumentHeader($data, $encryptionKey, $iv, $documentHeader, $user);
    
            // Step 5: Save the document body
            $this->saveDocumentBody($headerDocument->id, $encryptedBody, $checksum);
    
            if (!$id) {
                // For a new document, log the audit and create the initial version
                $changesSummary = DocumentServiceImpl::generateChangesSummary($documentHeader, $data);
                DocumentServiceImpl::logAudit($user->id, $headerDocument->id, $changesSummary, 'insert');
                $this->createVersion($headerDocument->id, $changesSummary);
            }
    
            DB::commit();
    
            return $headerDocument;
    
        }
        
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    


    private function generateEncryptionKey()
    {
        return Str::random(32);
    }

    private function encryptKey($encryptionKey)
    {
        return Crypt::encryptString($encryptionKey);
    }

    private function generateIV()
    {
        return substr(hash('sha256', 'iv-key'), 0, 16); // Example of IV generation
    }

    private function saveDocumentHeader(array $data, $encryptedKey, $iv, DocumentHeader $documentHeader, $user)
    {
        $documentHeader->user_id = $user->id;
        $documentHeader->module = $data['module'];
        $documentHeader->version = $data['version'];
        $documentHeader->metadata = json_encode($data['metadata']);
        $documentHeader->encryption_key = $encryptedKey;
        $documentHeader->iv = $iv;
        $documentHeader->save();

        return $documentHeader;
    }

    private function encryptDocument($header, $body, $encryptionKey, $iv)
    {
        // Ensure the encryption key is 32 bytes for aes-256-cbc
        if (strlen($encryptionKey) !== 32) {
            throw new Exception('Encryption key must be 32 bytes for AES-256-CBC.');
        }
    
        // Encrypt the header and body
        $encryptedHeader = openssl_encrypt($header, 'AES-256-CBC', $encryptionKey, 0, $iv);
        $encryptedBody = openssl_encrypt($body, 'AES-256-CBC', $encryptionKey, 0, $iv);
    
        if (!$encryptedHeader || !$encryptedBody) {
            throw new Exception('Encryption failed.');
        }
    
        // Base64-encode the encrypted data for storage
        return [base64_encode($encryptedHeader), base64_encode($encryptedBody)];
    }
    

    private function generateChecksum($encryptedBody)
    {
        return hash('sha256', $encryptedBody);
    }

    private function saveDocumentBody($documentId, $encryptedBody, $checksum)
    {
        $documentBody = new DocumentBody();
        $documentBody->document_id = $documentId;
        $documentBody->body = $encryptedBody;
        $documentBody->checksum = $checksum;
        $documentBody->save();
    }

   

    public function createVersion($documentId, $changesSummary)
    {
        try {
            // Retrieve the document header
            $documentHeader = DocumentHeader::findOrFail($documentId);
    
            // Determine the new version number
            $newVersionNumber = $documentHeader->version ? $documentHeader->version + 1 : 1;
    
            // Update the document header version
            $documentHeader->version = $newVersionNumber;
            $documentHeader->save();
    
            // Create a new version record
            $version = new Version();
            $version->document_id = $documentId;
            $version->version_number = $newVersionNumber;
            $version->changes_summary = json_encode($changesSummary);
            $version->save();
    
            return $version;
    
        } 
        
        catch (Exception $e) {
            
            Log::error('Error in createVersion: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
    
    

    public function getVersions($documentId)
    {
        $versions = Version::where('document_id', $documentId)
            ->orderBy('version_number', 'desc')
            ->get();

        return $versions;
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $user->load('role'); 
        if (!$user->role) {
            return 'User has no assigned role';
        }
    
        // Fetch the document to be deleted
        $document = DocumentHeader::find($id);
    
        // Check if the document exists
        if (!$document) {
            return 'Document not found';
        }
    
        if (!$user->hasPermission('delete document')) {
            return 'Unauthorized';
        }
    
        DB::beginTransaction();
    
        try {

            $document->delete();
    
            DocumentServiceImpl::logAudit(Auth::user()->id, $id, [], 'delete');
    
            DB::commit();
    
            return 'Document soft deleted successfully';
        } 
        
        catch (\Exception $e) {
            DB::rollBack();
    
            return 'An error occurred while deleting the document';
        }
    }
    

    public function searchDocuments(array $filters)
    {
        $query = DocumentHeader::query();
    
        // Apply filters based on user input
        if (isset($filters['module'])) {
            $query->where('module', $filters['module']);
        }
    
        if (isset($filters['tags'])) {
            $tags = $filters['tags'];
            $tagsArray = explode(',', $tags);
            foreach ($tagsArray as $tag) {
                $query->orWhereRaw("JSON_CONTAINS(metadata, ?)", [json_encode([$tag])]);
            }
        }
    
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
    
        // Get the results
        return $query->get();
    }
    
  
    public function fetchAndCombineDocument($documentId)
    {
        DB::beginTransaction();
    
        try {
            Log::debug('Starting fetchAndCombineDocument for document ID: ' . $documentId);
    
            $documentHeader = DocumentHeader::findOrFail($documentId);
            $documentBody = DocumentBody::where('document_id', $documentId)->firstOrFail();
    
            Log::debug('Fetched document header and body successfully', [
                'documentHeader' => $documentHeader,
                'documentBody' => $documentBody,
            ]);
    
            $encryptionKey = $documentHeader->encryption_key;
            Log::debug('Decrypted encryption key successfully.', ['encryptionKey' => $encryptionKey]);
    
            $iv = $documentHeader->iv;
            if (!$iv || strlen($iv) !== 16) {
                Log::error('Invalid IV retrieved.', ['iv' => $iv]);
                throw new Exception('Invalid IV. Decryption cannot proceed.');
            }
            Log::debug('IV retrieved successfully.', ['iv' => $iv]);
    
            // Decrypting the header and body
            $decryptedHeader = $documentHeader->module;  
            $decryptedBody = $documentHeader->decryptBody($documentBody->body, $encryptionKey, $iv);
    
            Log::debug('Decryption attempt completed.', [
                'decryptedHeader' => $decryptedHeader,
                'decryptedBody' => $decryptedBody,
            ]);
    
            // Check if decryption of the body was successful
            if (!$decryptedBody) {
                $opensslError = openssl_error_string();
                Log::error('Decryption failed for the body.', ['openssl_error' => $opensslError]);
                throw new Exception('Decryption failed for the body.');
            }
    
            // Log the decrypted body content for debugging
            Log::debug('Decrypted body content:', ['decryptedBody' => $decryptedBody]);
    
            // Calculate checksum and verify integrity
            $calculatedChecksum =$this->generateChecksum($documentBody->body);
            Log::debug('Calculated checksum:', [
                'calculatedChecksum' => $calculatedChecksum,
                'expectedChecksum' => $documentBody->checksum,
            ]);
    
            if ($calculatedChecksum !== $documentBody->checksum) {
                Log::error('Checksum verification failed.', [
                    'calculatedChecksum' => $calculatedChecksum,
                    'expectedChecksum' => $documentBody->checksum,
                ]);
                throw new Exception('Checksum verification failed. The document body may have been tampered with.');
            }
    
            // Sanitize the decrypted content to prevent injection attacks
            $sanitizedHeader = htmlspecialchars($decryptedHeader, ENT_QUOTES, 'UTF-8');
            $sanitizedBody = htmlspecialchars($decryptedBody, ENT_QUOTES, 'UTF-8');
            Log::debug('Sanitized decrypted content successfully.');
    
            // Combine the header and body
            $combinedDocument = [
                'header' => $sanitizedHeader,
                'body' => $sanitizedBody,
            ];
            Log::debug('Combined document created successfully.', $combinedDocument);
    
            // Log an audit entry
            DocumentServiceImpl::logAudit(Auth::user()->id, $documentId, ['action' => 'view'], 'view');
            Log::info('Audit log created for document access.', [
                'user_id' => Auth::user()->id,
                'document_id' => $documentId,
            ]);
    
            DB::commit();
    
            return [
                'status' => true,
                'data' => $combinedDocument,
            ];
    
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in fetchAndCombineDocument function.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
   public function searchDocumentsWithFilters(array $filters)
    {
        $query = DocumentHeader::query();
    
        if (isset($filters['search_term'])) {

            $query->where('module', 'LIKE', '%' . $filters['search_term'] . '%');
        }
    
        if (isset($filters['module'])) {
            $query->where('module', $filters['module']);
        }
    
        if (isset($filters['created_at'])) {
            $query->where('created_at', '>=', $filters['created_at']);
        }
    
        $documents = $query->with('bodies')->get();
    
        $result = $documents->map(function ($document) {
            $documentData = $document->toArray(); // Get the document as an array
            $documentData['bodies'] = $document->bodies->map(function ($body) use ($document) {

                return [
                    'decrypted_body' => $document->decryptBody(
                        $body->body,
                        $document->encryption_key,
                        $document->iv
                    ),
                ];
            });
    
            return $documentData;
        });
    
      return $result;
    }
    }
