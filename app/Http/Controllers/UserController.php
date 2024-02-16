<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Repositories\UserRepository;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	private $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function daftar(Request $req)
	{
		$data = $this->userRepository->getWhereAdvanced(
			fn ($q) => $q->where('fullname', 'like', "%{$req->keyword}%"),
			['updated_at', 'DESC'],
			null,
			null
		);

		return view('admin.pages.user.daftar', ['data' => $data]);
	}

	public function save(Request $req)
	{
		Validator::make($req->all(), [
			'fullname' => 'required|between:3,100',
			'email' => 'required|email|unique:users,email',
			'username' => 'required|between:4,50|unique:users,username|alpha_dash',
			'password' => 'nullable|min:6',
			'repassword' => 'same:password',
			'level' => 'required',
		])->validate();

		$result = $this->userRepository->create([
			'fullname' 	=> $req->fullname,
			'username' 	=> $req->username,
			'email' 	=> $req->email,
			'password' 	=> bcrypt($req->password),
			'level' 	=> $req->level
		]);

		if ($result) {
			alert()->success('Data Berhasil Tersimpan ke Database.', 'Tersimpan!')->autoclose(4000);
			return redirect()->route('admin.user');
		} else {
			alert()->info('Harap Periksa lagi data Formulir anda.', 'Tidak Tersimpan!')->autoclose(4000);
		}
	}

	public function edit($id)
	{
		$data = $this->userRepository->findById($id);
		return view('admin.pages.user.edit', ['rc' => $data]);
	}

	public function update(Request $req)
	{
		Validator::make($req->all(), [
			'fullname' => 'required|between:3,100',
			'username' => 'required|between:4,50|unique:users,username,' . $req->id . ',|alpha_dash',
			'email' => 'required|email|unique:users,email,' . $req->id,
			'password' => 'nullable|min:6',
			'repassword' => 'same:password',
			'level' => 'required',
		])->validate();

		if (!empty($req->password)) {
			$field = [
				'fullname' => $req->fullname,
				'username' => $req->username,
				'email' => $req->email,
				'level' => $req->level,
				'password' => bcrypt($req->password),
			];
		} else {
			$field = [
				'fullname' => $req->fullname,
				'username' => $req->username,
				'email' => $req->email,
				'level' => $req->level,
			];
		}

		$result = $this->userRepository->update($field, $req->id);

		if ($result) {
			alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(4000);
			return redirect()->route('admin.user');
		} else {
			alert()->info('Harap Periksa lagi data Formulir anda.', 'Tidak Tersimpan!')->autoclose(4000);
		}
	}

	public function delete(Request $req)
	{
		$result = $this->userRepository->delete($req->id);

		if ($result) {
			alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
			return redirect()->route('admin.user');
		}
	}

	public function exportExcel()
	{
		return Excel::download(new UsersExport, 'DataUsers.xlsx');
	}
}
