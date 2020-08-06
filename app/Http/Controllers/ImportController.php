<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImportResultResource;
use App\Import\Exceptions\ImportException;
use App\Import\Importer;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $importResult = Importer::forFile($request->file->path())->import();
        } catch (ImportException $e) {
            abort(500, 'Import failed!');
        }

        return new ImportResultResource($importResult);
    }
}
