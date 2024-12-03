<?php

namespace App\Services;

use App\Jobs\ProcessDocumentEncryption;
use App\Models\AuditLog;
use App\Models\DocumentHeader;
use App\Repositories\DocumentRepositoryImp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentServiceImpl implements DocumentService
{
    private DocumentRepositoryImp $documentRepository;

    public function __construct(DocumentRepositoryImp $documentRepository) 
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Store a document key.
     *
     * @param Request $request
     * @return mixed
     */
    public function storeDocumentKey(Request $request)
    {
        $id = $request->id;

        // Validate incoming request
        $data = $request->validate([
            'module' => 'required|string',
            'version' => 'required|integer',
            'metadata' => 'required|array',
            'header' => 'required|string',
            'body' => 'required|string',
        ]);

        return $this->documentRepository->storeOrUpdateDocumentKey($data, $id);
    }


    /**
     * Generate changes summary by comparing two document states.
     *
     * @param object $oldObject
     * @param array|object $newData
     * @return array
     */
   /**
 * Generate changes summary by comparing two document states.
 *
 * @param object $oldObject
 * @param array|object $newData
 * @return array
 */
public static function generateChangesSummary($oldObject, $newData)
{
    // Ensure the first parameter is an object
    if (!is_object($oldObject)) {
        throw new \InvalidArgumentException("The first argument must be an object.");
    }

    // Ensure the second parameter is an array or an object
    if (!is_array($newData) && !is_object($newData)) {
        throw new \InvalidArgumentException("The second argument must be an array or object.");
    }

    $reflection = new \ReflectionClass($oldObject);
    $properties = $reflection->getProperties();

    $changes = [];
    foreach ($properties as $property) {
        $property->setAccessible(true);

        // Get the old value
        $oldValue = $property->getValue($oldObject);

        // Get the new value (array or object handling)
        $propertyName = $property->getName();
        $newValue = is_array($newData) ? ($newData[$propertyName] ?? null) : $property->getValue($newData);

        // Compare values and add to changes if different
        if ($oldValue !== $newValue) {
            $changes[$propertyName] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }
    }

    return $changes;
}

       
    /**
     * Log an audit entry for document changes.
     *
     * @param int $userId
     * @param int $documentId
     * @param array $changesSummary
     * @return void
     */
    public static function logAudit($userId, $documentId, array $changesSummary,$action): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'document_id' => $documentId,
            'action' => $action,
            'changes_summary' => json_encode($changesSummary),
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function getVersions($documentId){

        return $this->documentRepository->getVersions($documentId);
    }

    public function destroy($id){

        return $this->documentRepository->destroy($id);
    }

    public function searchDocuments(Request $request)
    {
        // Get filters from the request
        $filters = $request->only(['module', 'user_id', 'tags']);
        
        // Call the service to perform the search
        $documents = $this->documentRepository->searchDocuments($filters);

        // Return the results as a JSON response
        return $documents;
    }

    public function fetchAndCombineDocument($documentId)
    {

        return response()->json($this->documentRepository->fetchAndCombineDocument($documentId));
    }
  
    public function searchDocumentsWithFilters(Request $request)
    {
        // Validate the input
        $data = $request->validate([
            'search_term' => 'sometimes|string',
            'module' => 'sometimes|string',
            'created_at' => 'sometimes|date',
        ]);

       
        return $this->documentRepository->searchDocumentsWithFilters($data);
    }

    public function storeOrUpdateDocumentKeyUsingQueue(Request $request)
    {
        // Authenticate the user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        $data = $request->validate([
            'module' => 'required|string',
            'version' => 'required|integer',
            'metadata' => 'required|array',
            'header' => 'required|string',
            'body' => 'required|string',
        ]);
    
        // Dispatch the job with validated data
        ProcessDocumentEncryption::dispatch($this->documentRepository,$data);
    
        return 'Successfully saved data.';
    }
    
}
