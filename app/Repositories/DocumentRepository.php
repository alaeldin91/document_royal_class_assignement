<?php
  namespace App\Repositories;

  interface DocumentRepository
  {
  
    public function storeOrUpdateDocumentKey(array $data,$id = null);
    public function getVersions($documentId);
    public function destroy($documentId);
    public function searchDocuments(array $filters);
    public function fetchAndCombineDocument($documentId);
    public function searchDocumentsWithFilters(array $filters);

    
  }