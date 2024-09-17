<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Imagick;
use App\Models\{Document, TypeDocument, Demande, DemandeEtape};

class FileUpload extends Controller
{
    public function upload(Request $request) {

        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $name = $file->hashName(); // Generate a unique, random name...
            $extension = $file->extension();

            $path = $request->file('file')->storeAs('public/documents', "$name.$extension");

            //Creer le preview pour un fichier pdf
            $thumbnail = $this->createThumnail($name, $path);

            if($request->document == "undefined") {

                $etape = DemandeEtape::whereDemandeId($request->demande)
                    ->whereEtapeId($request->etape)->first();

                if($etape) {
                    $etape->update([
                        'recu_paiement' => $path,
		                'recu_paiement_preview' => $thumbnail,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'refresh' => true,
                    'file_url' => asset(Storage::url($thumbnail))
                ]);
            }

            $document = Document::firstOrCreate([
                'type_document_id' => $request->document,
		        'demande_id' => $request->demande,
		        'etape_id' => $request->etape,
            ]);

            $document->update([
                'status' => null,
                'motif_rejet' => null
            ]);

            //Supprimer les fichier existant
            if($document->file AND file_exists(\storage_path("app/{$document->file}"))) {
                unlink(\storage_path("app/{$document->file}"));
                if(file_exists(\storage_path("app/{$document->preview}"))) {
                    unlink(\storage_path("app/{$document->preview}"));
                }
            }

            $document->update([
                'file' => $path,
		        'preview' => $thumbnail,
            ]);

            return response()->json([
                'success' => true,
                'refresh' => false,
                'file_url' => asset(Storage::url($thumbnail))
            ]);

        } else {
            return response()->json(['success' => false]);
        }

    }

    function createThumnail($name, $path, $sub_path = 'documents')
    {
        $thumbnail = $path;

        if($this->checkType($path) == "pdf") {

            $pdf = new Imagick();

            // Lire le fichier PDF
            $pdf->readImage(\storage_path("app/$path"));

            // Sélectionner la première page
            $pdf->setIteratorIndex(0);

            // Convertir la page en image
            $pdf->setImageFormat('png');
            $image = $pdf->getImageBlob();

            // Enregistrer l'image
            $imagePath = "public/$sub_path/$name-thumbnail.png";
            file_put_contents(storage_path("app/$imagePath"), $image);

            $thumbnail = $imagePath;
        }

        return $thumbnail;
    }

    public function checkType($file)
	{
		$fileExtension = pathinfo(storage_path("app/$file"), PATHINFO_EXTENSION);

        // Vérifier si c'est une image
        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']);

        // Vérifier si c'est un PDF
        $isPDF = (strtolower($fileExtension) === 'pdf');

        // Afficher le résultat
        if ($isImage) {
            return "image";
        } elseif ($isPDF) {
            return "pdf";
        }

        return null;
	}
}
