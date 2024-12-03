<?php
  namespace App\Services;

use Illuminate\Http\Request;

  interface DocumentService
  {
  
    public function storeDocumentKey(Request $request);
    public function getVersions($documentId);
    public function searchDocuments(Request $request);
    public function destroy($id);
    public function fetchAndCombineDocument($documentId);
    public function searchDocumentsWithFilters(Request $request);

    
  }