<?php
namespace Kwasett\LaravelCommon\Controller;

use Kwasett\LaravelCommon\Service\CrudBaseInterface;
use Kwasett\LaravelCommon\Utils\ProjectLog;
use Kwasett\LaravelCommon\Utils\CommonStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BaseResourceController extends Controller
{

    public $crudService;
    public $page = "";
    public $extra = [];

    public function __construct(CrudBaseInterface $crudService, $page)
    {
        $this->crudService = $crudService;
        $this->page = $page;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view("{$this->page}.create", ['status' => CommonStatus::$status, 'xtra' => $this->extra]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $created = $this->crudService->create($data);

        if ($created->isError) {
            ProjectLog::debug("Store has errors : " . json_encode($created->errors));
            return Redirect::to("/{$this->page}/create")
                ->with('flash_error', 'true')
                ->withInput()
                ->withErrors($created->errors);
        }


        Session::flash('message', $created->statusDescription);
        return $this->index($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $list = $this->crudService->search($data);


        Log::info("{$this->page} found : " . count($list));
        return view("{$this->page}.index", ['items' => $list, 'status' => CommonStatus::$status, 'xtra' => $this->extra]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $item = $this->crudService->find($id);
        if ($item) {
            Session::flash('error', "Item Not Found");
        }

        return view("{$this->page}.edit", ['item' => $item, 'status' => CommonStatus::$status, 'xtra' => $this->extra]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        ProjectLog::debug("About Updating");
        $data = $request->all();
        return $this->updateWithData($request, $id, $data);
    }

    public function updateWithData(Request $request, $id, $data)
    {
        $edited = $this->updateWithData($request, $id, $data);
        if ($edited->isError) {
            return Redirect::to("/{$this->page}/edit")
                ->with('flash_error', 'true')
                ->withInput()
                ->withErrors($edited->errors);
        }
        Session::flash('message', $edited->statusDescription);
        return $this->index($request);
    }

    public function updateWithDataOnly(Request $request, $id, $data)
    {
        ProjectLog::debug("About Updating");
        return $this->crudService->update($id, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id)
    {

        $this->crudService->delete($id);
        Redirect::to("/{$this->page}");

    }

    public function setExtras($extra)
    {
        $this->extra = $extra;
    }
}
