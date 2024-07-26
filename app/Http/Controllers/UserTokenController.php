<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use App\Http\Requests\StoreUserTokenRequest;
use App\Http\Requests\UpdateUserTokenRequest;

class UserTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserTokenRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserTokenRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserToken  $userToken
     * @return \Illuminate\Http\Response
     */
    public function show(UserToken $userToken)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserToken  $userToken
     * @return \Illuminate\Http\Response
     */
    public function edit(UserToken $userToken)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserTokenRequest  $request
     * @param  \App\Models\UserToken  $userToken
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserTokenRequest $request, UserToken $userToken)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserToken  $userToken
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserToken $userToken)
    {
        //
    }
}
