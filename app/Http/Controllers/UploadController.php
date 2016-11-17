<?php

namespace App\Http\Controllers;

use App\Album;
use App\Events\FileWasUploaded;
use App\Http\Requests\AvatarUploadRequest;
use App\Http\Requests\FileUploadRequest;
use App\Image;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    public function upload(FileUploadRequest $request)
    {
        $f = $request->file("file");
        if($request->has("avatar"))
            $album = null;
        else
            $album = Album::findOrFail($request->get("album"));

        $user = Auth::user();
        $image = new Image();
        $image->filename = $f->getClientOriginalName();
        $image->album_id = $album->id;
        $image->user_id = Auth::user()->id;
        //dd($image);
        $image->save();

        $f->storePubliclyAs(dirname($image->path),$image->filename);
        event(new FileWasUploaded($image));
        if($request->ajax())
            return $image;//just return the model
        return redirect()->to(route("album.show",$album->id))->with("message","Image uploaded successfully");
    }


}
