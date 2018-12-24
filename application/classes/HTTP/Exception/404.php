<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {

    /**
     * Generate a Response for the 404 Exception.
     *
     * The user should be shown a nice 404 page.
     *
     * @return Response
     * @throws Kohana_Exception
     * @throws View_Exception
     */
    public function get_response(): Response
    {
        GRGEError::i();
        $response = Response::factory()->status(200);

        if ($this->_request->headers('X-Requested-With') === 'XMLHttpRequest') {

            $response->headers('Content-Type', 'application/json; charset=utf-8');

            if ($this->_request->action() === 'japi')
                $response->body(json_encode(['error' => array(
                    'code' => grge\E_HTTP_REQUEST_INVALID,
                    'name' => GRGEError::r(grge\E_HTTP_REQUEST_INVALID),
                    'message' => GRGEError::d(grge\E_HTTP_REQUEST_INVALID),
                    'details' => ['uri' => $this->_request->uri()],
                )], JSON_FORCE_OBJECT));
            else
                $response->body(json_encode(['content' => ['content' => View::factory('pages/notfound')->set('uri', $this->_request->uri())->render()]], JSON_FORCE_OBJECT));

        } else
            $response->body(View::factory('redirect')->set('url',URL::base())->set('path',$this->_request->uri()));

        return $response;
    }

}
