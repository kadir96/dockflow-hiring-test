<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Http\Resources\ImportResultResource;
use App\Import\Exceptions\ImportException;
use App\Import\Importer;

class ImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        try {
            $importResult = Importer::forFile($request->file->path())->import();
        } catch (ImportException $e) {
            abort(500, 'Import failed!');
        }

        return new ImportResultResource($importResult);
    }
}
