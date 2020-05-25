<?php


namespace App\Utilities;


use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionMedia
{
    private $mediaPresentation;
    private $mediaUpload;
    private $mediaActualite;
    private $mediaSlide;

    public function __construct($presentationDirectory, $mediaDirectory, $actualiteDirectory, $sliderDirectory)
    {
        $this->mediaPresentation = $presentationDirectory;
        $this->mediaUpload = $mediaDirectory;
        $this->mediaActualite = $actualiteDirectory;
        $this->mediaSlide = $sliderDirectory;
    }

    public function upload(UploadedFile $file, $media = null)
    {
        $slugify = new Slugify();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slugify($originalFileName);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'photo') $file->move($this->mediaPresentation, $newFilename);
            elseif ($media === 'media') $file->move($this->mediaSlide, $newFilename);
            elseif ($media === 'actualite') $file->move($this->mediaActualite, $newFilename);
            else $file->move($this->mediaUpload, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;

    }
}