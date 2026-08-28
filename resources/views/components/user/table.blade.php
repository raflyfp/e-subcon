 <div class="dt-responsive table-responsive">
     <table id="cbtn-selectors" class="table table-striped table-bordered nowrap">
         <thead>
             <tr class="text-center">
                 <th>No</th>
                 <th>Nama</th>
                 <th>No Karyawan</th>
                 {{-- <th>Action</th> --}}
             </tr>
         </thead>
         <tbody>
             @forelse ($user as $item)
                 <tr class="text-center">
                     <td>{{ $loop->iteration }}</td>
                     <td>{{ $item->name }}</td>
                     <td>{{ $item->username }}</td>
                     {{-- button edit dan hapus on progress --}}
                     {{-- <td>
                         <div class="row">
                             <div class="col">
                                 <button type="button" class="btn btn-warning">Edit</button>

                                 <button type="button" class="btn btn-danger">Hapus</button>
                             </div>
                         </div>
                     </td> --}}
                 </tr>
             @empty
                 <tr>
                     <td colspan="5" class="text-center">Data karyawan kosong</td>
                 </tr>
             @endforelse
         </tbody>
     </table>
 </div>
