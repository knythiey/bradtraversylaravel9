<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // show all listing
    public function index()
    {
        return view('listings.index', [
            'listings' => Listing::latest()
                ->filter(
                    request(['tag', 'search'])
                )->paginate(6),
        ]);
    }

    // Single listing
    public function show(Listing $listing)
    {
        return view('listings.show', [
            'listing' => $listing,
        ]);
    }

    // Show create form
    public function create()
    {
        return view('listings.create');
    }

    // Store Listing Data
    public function store(Request $request)
    {
        $postData = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'nullable',
        ]);

        if ($request->hasFile('logo')) {
            $postData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $postData['user_id'] = auth()->id();

        Listing::create($postData);

        return redirect('/')->with('message', 'Listing Created Successfully!');
    }

    // Show Edit Form
    public function edit(Listing $listing)
    {
        return view('listings.edit', ['listing' => $listing]);
    }

    // Update Data
    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id != auth()->id()) {
            abort(403, 'Unathorized Action');
        }

        $postData = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')->ignore($listing)],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'nullable',
        ]);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($listing->logo);
            $postData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($postData);

        return back()->with('message', 'Listing Updated Successfully!');
    }

    // Delete Listing
    public function destroy(Listing $listing)
    {
        if ($listing->user_id != auth()->id()) {
            abort(403, 'Unathorized Action');
        }

        Storage::disk('public')->delete($listing->logo);
        $listing->delete();
        return redirect('/')->with('message', 'Listing Removed Successfully!');
    }

    public function manage()
    {
        return view('listings.manage', [
            'listings' => auth()->user()->listings()->get(),
        ]);
    }
}
