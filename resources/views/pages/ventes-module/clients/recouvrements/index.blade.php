@extends('layout.template')

@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex">
        <div class="col-12">
            <h1 class="float-left">Récouvrements clients </h1>
        </div>
    </div><!-- End Page +++ -->

    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if ($message = session('message'))
            <div class="alert alert-success alert-dismissible">
                {{ $message }}
            </div>
            @endif

            @if ($message = session('error'))
            <div class="alert alert-danger alert-dismissible">
                {{ $message }}
            </div>
            @endif

            <div class="card-header">
                <a data-bs-toggle="modal" data-bs-target="#addRecouvrement" class="btn btn-dark text-white btn-sm">
                    <i class="fas fa-solid fa-plus"></i>
                    Ajouter
                </a>
            </div>

            <br>
            <!-- RECHERCHER AR CLIENT -->
            <div class="row justify-content-center d-flex">
                <div class="col-md-6">
                    <form class="border  p-3 rounded bg-light" action="{{route('recouvrement.index')}}" method="get">
                        <select name="client" class="form-control form-select select2" required>
                            <option value="">Selectionnez un client</option>
                            @foreach($clients as $client)
                            <option
                                value="{{$client->id}}">{{$client->nom_client}}
                            </option>
                            @endforeach
                        </select>
                        @error("client")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        <div class="">
                            <button class="w-100 mt-3 btn btn-primary" type="submit">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <form action="{{route('recouvrement.verification')}}" method="post">
                <!--  -->
                @if(Auth::user()->hasRole("Super Admin") || Auth::user()->hasRole("CHARGE DES STOCKS ET SUIVI DES ACHATS"))
                <button type="submit" class="btn btn-success btn-sm btn-dark"><i class="bi bi-check-circle"></i> Vérifier</button>
                <!--  -->
                <br> <br>
                @endif

                @csrf
                <table id="example" class="table table-bordered border-warning  table-hover table-warning table-sm"
                    style="font-size: 12px">
                    <thead >
                        <tr >
                            <th >Vérification</th>
                            <th >Client</th>
                            <th >Recouvreur</th>
                            <th >Commentaire</th>
                            <th >Date</th>
                            <th >Statut</th>
                            <th >Vérifié par</th>
                            <th >Vérifié le</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($recouvrements as $recouvrement)
                        <tr>
                            <td>
                                @if(Auth::user()->hasRole("Super Admin") || Auth::user()->hasRole("CHARGE DES STOCKS ET SUIVI DES ACHATS"))
                                <div class="form-check text-center">
                                    <input @if($recouvrement->verified) disabled checked @endif class="form-check-input form-control" style="width: 20px;" type="checkbox" name="recouvrements[]" value="{{$recouvrement->id}}" id="checkIndeterminate">
                                </div>
                                @else
                                ---
                                @endif
                            </td>
                            <td class="ml-5 pr-5">{{ $recouvrement->client->nom_client }}</td>
                            <td class="ml-5 pr-5">{{ $recouvrement->user?$recouvrement->user->name:'---' }}</td>
                            <td class="text-center">
                                <textarea class="form-control" rows="1" id="" placeholder="{{$recouvrement->comments}}"></textarea>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-danger">{{ \Carbon\Carbon::parse($recouvrement->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                            </td>
                            <td class="text-center">
                                @if($recouvrement->verified)
                                <span class="badge bg-success">Vérifié</span>
                                @else
                                <span class="badge bg-danger">Non Vérifié</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $recouvrement->verifiedBy?$recouvrement->verifiedBy->name:"---" }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-danger">@if($recouvrement->verified_at){{ \Carbon\Carbon::parse($recouvrement->verified_at)->locale('fr')->isoFormat('D MMMM YYYY') }} @else '---' @endif </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot >
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Recouvreur</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Vérifié par</th>
                            <th>Vérifié le</th>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- AJOUT DE RECOUVREMENT -->
    <div class="modal fade" id="addRecouvrement">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nouveau Recouvrement</h4>
                    <button type="button" class="btn btn-sm bg-light text-danger" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('recouvrement.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <select name="client_id" class="form-control form-select select2-add" required>
                                <option value="">Selectionnez un client</option>
                                @foreach($clients as $client)
                                <option value="{{$client->id}}">{{$client->nom_client}}</option>
                                @endforeach
                            </select>
                            @error("client")
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="mb-3 form-group">
                            <textarea name="comments" class="form-control" rows="3" id="" placeholder="Commentaire ...." required></textarea>
                            @error("comments")
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="w-100 btn btn-success btn-block">Enregistrer
                            <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- FIN RECOUVREMENT -->
</main>
<!-- /.content -->
@endsection


@section('scripts')
<script>
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('.select2-add').select2({
        // theme: 'bootstrap-5',
        // width: '100%',
        dropdownParent: $('#addRecouvrement')
    });

</script>
@endsection