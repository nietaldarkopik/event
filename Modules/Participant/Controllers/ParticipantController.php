<?php
namespace Modules\Participant\Controllers;

use App\Http\Controllers\Controller;
use \Modules\Participant\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index()
    {
        $items = Participant::latest()->get();
        return view('admin.participants.index', compact('items'));
    }

    public function create()
    {
        $users = \Modules\Authentication\Models\User::all();

        return view('admin.participants.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'user_id' => 'nullable',
  'full_name' => 'required',
  'gender' => 'nullable',
  'birth_date' => 'nullable',
  'institution' => 'nullable',
  'profession' => 'nullable',
  'phone' => 'nullable',
  'address' => 'nullable',
  'photo' => 'nullable',
));

        Participant::create($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Profil Peserta berhasil ditambahkan.');
    }

    public function show(Participant $participant)
    {
        return view('admin.participants.show');
    }

    public function edit(Participant $participant)
    {
        $users = \Modules\Authentication\Models\User::all();

        return view('admin.participants.edit', compact('users'));
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate(array (
  'user_id' => 'nullable',
  'full_name' => 'required',
  'gender' => 'nullable',
  'birth_date' => 'nullable',
  'institution' => 'nullable',
  'profession' => 'nullable',
  'phone' => 'nullable',
  'address' => 'nullable',
  'photo' => 'nullable',
));

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Profil Peserta berhasil diperbarui.');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        return redirect()->route('admin.participants.index')
            ->with('success', 'Profil Peserta berhasil dihapus.');
    }
}
