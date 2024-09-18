<?php

namespace App\Http\Controllers;

use App\Models\ProductionTicket;
use Illuminate\Http\Request;

class ticketFromplaneCompany extends Controller
{
    public function AddticketsFromplaneCompany(Request $request){
        $validatedData = $request->validate([
            'Ticket_Destination' => 'required',
            'price_from_plane_company'=>'required|min:0'
        
        ]);
        ProductionTicket::create([
            'Ticket_Destination'=>$request->Ticket_Destination,
            'price_from_plane_company'=>$request->price_from_plane_company
        ]);
    return response()->json([
        'message'=>'ticket Added successfully'
    ]);}}
