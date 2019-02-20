<?php


class PostXssController extends \Illuminate\Routing\Controller {

    public function getIndex()
    {
        return response()->json(request()->all());
    }
}
