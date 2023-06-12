<x-app-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        a {
                text-decoration: none;
            }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="d-flex justify-content-end mb-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Tambahkan Mall
                    </button>
                </div>
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mall</th>
                            <th>Alamat</th>
                            <th>ID Owner Mall</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($malls as $key=>$mall)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td class="width:200px">{{ $mall->namaMall }}</td>
                                <td>{{ $mall->alamatMall }}</td>
                                <td>
                                    <form action="/changeOwner" method="POST"> @csrf
                                        <input type="hidden" name="mall_id" value="{{ $mall->id }}">
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="text" class="form-control" name="user_id" value="{{ $mall->user_id }}">
                                            </div>
                                            <div class="col">
                                                <button type="submit" class="btn btn-secondary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form action="/deleteMall" method="POST"> @csrf
                                        <input type="hidden" name="mall_id" value="{{ $mall->id }}">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>                        
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="/tambah-mall" method="POST">@csrf
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Tambahkan Mall</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <label for="exampleInputPassword1" class="form-label">Nama Mall</label>
                  <input type="text" class="form-control" name="namaMall">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Tambahkan Mall</button>
                </div>
            </form>
          </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready( function () {
            $('#myTable').DataTable({
                scrollX: true,
                "columnDefs": [
                    { "width": "5px", "targets": 0 },
                    { "width": "200px", "targets": 1 },
                    { "width": "80%", "targets": 2 },
                    { "width": "200px", "targets": 3 },
                    { "width": "5px", "targets": 4 }
                ]
            });
        } );
    </script>
</x-app-layout>
