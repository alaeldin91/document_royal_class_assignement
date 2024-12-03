<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentHeader;
use App\Services\DocumentServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessDocumentEncryption;


class DocumentController extends Controller
{
    private DocumentServiceImpl $documentService;

    public function __construct(DocumentServiceImpl $documentService) {
        $this->documentService = $documentService;
    }
   
    public function storeDocumentKey(Request $request){

        return response()->json(['status'=>true,'data'
        =>$this->documentService->storeDocumentKey($request)],200);
    }

    public function getVersions($documentId)
    {
          return  response()->json(['status'=>true,
          'data'=>$this->documentService->getVersions($documentId)]);
    }

    public function destroy($documentId)
    {
        return response()->json(['status'=>true,
        'data'=> $this->documentService->destroy($documentId)],200);
       }

    public function searchDocuments(Request $request)
    {
        return  response()->json(['status'=>true,'data'=> $this->documentService->searchDocuments($request)],200); 
    }

    public function fetchAndCombineDocument($documentId)
    {
        return response()->json(['status'=>true
        ,'data'=> $this->documentService
        ->fetchAndCombineDocument($documentId)],200); 
        
    }

    public function searchDocumentsWithFilters(Request $request)
    {
        $results = $this->documentService->searchDocumentsWithFilters($request);

        return response()->json([
            'status' => true,
            'data' => $results,
        ],200);
    }
   
    public function storeOrUpdateDocumentKeyUsingQueue(Request $request)
    {
        // Authenticate the user
       
        // Return the stored document
        return response()->json(['status'=>true,'document'
         => $this->documentService->storeOrUpdateDocumentKeyUsingQueue($request)], 200);
    }
    
   }
