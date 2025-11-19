<?php

namespace App\Http\Controllers;

use App\Models\Population;
use Illuminate\Http\Request;

class PopulationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'biomassa' => 'required|numeric',
            'waktu' => 'required|date'
        ]);

        Population::create($request->all());
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, Population $population)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'biomassa' => 'required|numeric',
            'waktu' => 'required|date'
        ]);

        $population->update($request->all());
        return redirect()->back();
    }

    public function destroy(Population $population)
    {
        $population->delete();
        return redirect()->back();
    }
}
