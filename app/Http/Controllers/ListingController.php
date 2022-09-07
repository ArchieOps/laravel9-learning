<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ListingController extends Controller
{   
    // Show all listings
    public function index() {
        
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->Paginate(6)
        ]);
    }

    // Show Single listings
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing
        ]);
    }

    // Show Create Form
    public function create() {
        return view('listings.create');
    }

    // Store listing data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();
        
        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing created sucessfully');
    }

    // Show edit form
    public function edit(Listing $listing){
        return view('listings.edit', ['listing' => $listing]);
    }
    
    // Update listing data
    public function update(Request $request, Listing $listing) {

        if($listing->user_id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }


        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
        
        $listing->update($formFields);

        return back()->with('message', 'Listing updated sucessfully');
    }

    // Delete Listing
    public function delete(Listing $listing){
        $listing->delete();

        return redirect('/')->with('message', 'Listing deleted succesfully');
    }

    // Manage listings
    public function manage(){
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}
