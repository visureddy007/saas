<?php

/**
 * PageController.php - Controller file
 *
 * This file is part of the Page component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Page\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Base\BaseRequest;
use App\Yantrana\Components\Page\PageEngine;
use App\Yantrana\Components\Page\Models\PageModel;
use Illuminate\Validation\Rule;



class PageController extends BaseController
{
    /**
     * @var PageEngine - Page Engine
     */
    protected $pageEngine;

    /**
     * Constructor
     *
     * @param  PageEngine  $pageEngine  - Page Engine
     * @return void
     *-----------------------------------------------------------------------*/
    public function __construct(PageEngine $pageEngine)
    {
        $this->pageEngine = $pageEngine;
    }

    /**
     * list of Page
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function viewPage($vendor, $slug)
    {
        $pageData = $this->pageEngine->preparePageData($slug);

        // load the view
        return $this->loadView('page.page', [
            'pageData' => $pageData,
        ], [
            'compress_page' => false,
        ]);
    }

    /**
     * list of Page
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function showPageView()
    {
        // load the view
        return $this->loadView('page.list');
    }

    /**
     * list of Page
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function preparePageList()
    {
        // respond with dataTables preparations
        return $this->pageEngine->preparePageDataTableSource();
    }

    /**
     * Page process delete
     *
     * @param  mix  $pageIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function processPageDelete($pageIdOrUid, BaseRequest $request)
    {

        // ask engine to process the request
        $processReaction = $this->pageEngine->processPageDelete($pageIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Page create process
     *
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processPageCreate(BaseRequest $request)
    {
        // process the validation based on the provided rules
        $request->validate([
            'title' => 'required|min:2|max:255',
            'slug' => 'required|alpha_num|unique:pages',
            'description' => 'required|min:2',
            'show_in_menu'=> 'sometimes|required',
            'status' => 'sometimes|required',
        ]);
        // ask engine to process the request
        $processReaction = $this->pageEngine->processPageCreate($request->all());

        // get back with response
        return $this->processResponse($processReaction);
    }

    /**
     * Page get update data
     *
     * @param  mix  $pageIdOrUid
     * @return json object
     *---------------------------------------------------------------- */
    public function updatePageData($pageIdOrUid)
    {
        // ask engine to process the request
        $processReaction = $this->pageEngine->preparePageUpdateData($pageIdOrUid);

        // get back to controller with engine response
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Page process update
     *
     * @param  mix @param  mix $pageIdOrUid
     * @param  object BaseRequest $request
     * @return json object
     *---------------------------------------------------------------- */
    public function processPageUpdate(BaseRequest $request)
    {
        // process the validation based on the provided rules
        $request->validate([
            'pageIdOrUid' => 'required',
            'title' => 'required|min:2|max:255',
            'slug' =>['required','alpha_num',Rule::unique((new PageModel())->getTable())->ignore($request->get('pageIdOrUid'), '_uid')],
            'description' => 'required|min:2',
            'show_in_menu'=> 'sometimes|required',
            'status' => 'sometimes|required',
        ]);
        // ask engine to process the request
        $processReaction = $this->pageEngine->processPageUpdate($request->get('pageIdOrUid'), $request->all());

        // get back with response
        return $this->processResponse($processReaction, [], [], true);
    }

    

}
