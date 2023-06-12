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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Mall Information') }}
                            </h2>
                    
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your Mall profile information.") }}
                            </p>
                        </header>
                    
                        <form method="post" action="{{ route('mall.update') }}" class="mt-6 space-y-6">
                            @csrf
                            <input type="hidden" name="id" value="{{ $mall->id }}">
                            
                            <div>
                                <x-input-label for="fotoMall" :value="__('Foto Mall')" />
                                <x-text-input id="fotoMall" name="fotoMall" type="text" class="mt-1 block w-full" :value="old('fotoMall', $mall->fotoMall)" required autofocus autocomplete="fotoMall" />
                                <x-input-error class="mt-2" :messages="$errors->get('fotoMall')" />
                            </div>

                            <div>
                                <x-input-label for="namaMall" :value="__('Nama Mall')" />
                                <x-text-input id="namaMall" name="namaMall" type="text" class="mt-1 block w-full" :value="old('namaMall', $mall->namaMall)" required autofocus autocomplete="namaMall" />
                                <x-input-error class="mt-2" :messages="$errors->get('namaMall')" />
                            </div>
                            
                            <div>
                                <x-input-label for="alamatMall" :value="__('Alamat Mall')" />
                                <x-text-input id="alamatMall" name="alamatMall" type="text" class="mt-1 block w-full" :value="old('alamatMall', $mall->alamatMall)" required autofocus autocomplete="alamatMall" />
                                <x-input-error class="mt-2" :messages="$errors->get('alamatMall')" />
                            </div>
                            
                            <div>
                                <x-input-label for="openTimeMall" :value="__('Open Time Mall')" />
                                <x-text-input id="openTimeMall" name="openTimeMall" type="text" class="mt-1 block w-full" :value="old('openTimeMall', $mall->openTimeMall)" required autofocus autocomplete="openTimeMall" />
                                <x-input-error class="mt-2" :messages="$errors->get('openTimeMall')" />
                            </div>
                    
                            {{-- <div>
                                <x-input-label for="card_id" :value="__('ID Card')" />
                                <x-text-input id="card_id" name="card_id" type="text" class="mt-1 block w-full" :value="old('card_id', $user->card_id)" required autofocus autocomplete="card_id" />
                                <x-input-error class="mt-2" :messages="$errors->get('card_id')" />
                            </div> --}}
                    
                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                    
                                @if (session('status') === 'mall-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                    
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="">
                    <div class="d-flex justify-content-end mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Tambahkan Spot
                        </button>
                    </div>
                    <table id="myTable" class="display">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Lantai</th>
                                <th>Blok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spots as $key=>$spot)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $spot->id }}</td>
                                    <td>{{ $spot->lantai }}</td>
                                    <td>{{ $spot->blok }}</td>
                                    <td>{{ $spot->harga }}</td>
                                    {{-- <td><a class="btn btn-primary" href="/mymall/{{ $mall->id }}">Edit</a></td> --}}

                                    <td>
                                        <form action="/deleteSpot" method="POST"> @csrf
                                            <input type="hidden" name="id" value="{{ $spot->id }}">
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
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="/tambah-spot" method="POST">@csrf
                <input type="hidden" name="mall_id" value="{{ $mall->id }}">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Tambahkan Spot</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="exampleInputPassword1" class="form-label">Lantai</label>
                                <input type="number" class="form-control" name="lantai">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="exampleInputPassword1" class="form-label">Blok</label>
                                <input type="text" class="form-control" name="blok">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Harga</label>
                        <input type="text" class="form-control" name="harga">
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Tambahkan Spot</button>
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
                // scrollX: true,
                // "columnDefs": [
                //     { "width": "20%", "targets": 0 },
                //     { "width": "20%", "targets": 1 },
                //     { "width": "20%", "targets": 2 },
                //     { "width": "20%", "targets": 3 },
                //     { "width": "20%", "targets": 4 }
                // ]
            });
        } );
    </script>
</x-app-layout>
