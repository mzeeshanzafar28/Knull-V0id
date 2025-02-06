<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller {
    public function upload( Request $request ) {
        $request->validate( [ 'file' => 'required|file|max:102400' ] );
        // 100MB max

        $file = $request->file( 'file' );
        $storedName = Str::random( 40 ) . '.' . $file->extension();

        $path = $file->storeAs( 'uploads', $storedName );

        $fileRecord = File::create( [
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ] );

        return response()->json( [
            'file_id' => $fileRecord->file_id,
            'expires_at' => $fileRecord->expires_at
        ] );
    }

    public function download( Request $request ) {
        $request->validate( [ 'file_id' => 'required|uuid' ] );

        $file = File::where( 'file_id', $request->file_id )->firstOrFail();

        return Storage::download( 'uploads/' . $file->stored_name, $file->original_name, [
            'Content-Type' => $file->mime_type
        ] );
    }

    public function userFiles() {
        return File::where( 'user_id', auth()->id() )->get();
    }

    public function delete( File $file ) {
        if ( $file->user_id !== auth()->id() ) {
            abort( 403 );
        }

        Storage::delete( 'uploads/' . $file->stored_name );
        $file->delete();

        return response()->json( [ 'status' => 'File erased from existence' ] );
    }
}