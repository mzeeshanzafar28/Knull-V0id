<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncryptionController extends Controller {
    public function encrypt( Request $request ) {
        $request->validate( [
            'message' => 'required|string',
            'key' => 'required|string'
        ] );

        // Placeholder encryption logic ( replace with actual Kyber encryption )
        $encryptedMessage = base64_encode( $request->message );

        return response()->json( [ 'encrypted_message' => $encryptedMessage ] );
    }

    public function decrypt( Request $request ) {
        $request->validate( [
            'encrypted_message' => 'required|string',
            'key' => 'required|string'
        ] );

        // Placeholder decryption logic ( replace with actual Kyber decryption )
        $decryptedMessage = base64_decode( $request->encrypted_message );

        return response()->json( [ 'decrypted_message' => $decryptedMessage ] );
    }
}
