@extends('layout.template')
@section('content')
    <main id="main" class="main">
        <style>
            .ui-datepicker-disabled {
                opacity: 0.5;
            }
        </style>
        <div class="pagetitle">
            <h1>Ajout d'un acompte </h1>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <!-- Afficher des messages de succès -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Afficher des erreurs de validation -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">Enregistrer un acompte</h5>

                            <!-- Vertical Form -->
                            <form class="row g-3" action="{{ route('acompte.store') }}" method="POST">
                                @csrf
                                <div class="col-6 mb-3">
                                    <label class="form-label">Client</label>
                                    <select class="js-data-example-ajax form-control" name="client_id"
                                        id="client_select"></select>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">Montant acompte</label>
                                    <input type="text" class="form-control" value="{{ old('montant_acompte') }}"
                                        name="montant_acompte">
                                </div>

                                <div class="col-6 mb-3">
                                    <label class="form-label">Type de règlement</label>
                                    <select name="type_reglement" class="form-control" id="type_reglement">
                                        <option value="Espèce ">En espèce </option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Virement">Virement</option>
                                        <option value="Autres">Autres</option>
                                    </select>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">Référence</label>
                                    <input type="text" class="form-control" name="reference"
                                        value="{{ old('reference') }}">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <div class="loader"></div>

                                    <button type="reset" class="btn btn-secondary">Annuler</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <script>
         var apiUrl = "{{ config('app.url_ajax') }}";

            $(document).ready(function() {
                $('.js-data-example-ajax').select2({
                    placeholder: 'Selectionner client',
                    ajax: {
                        url: apiUrl + '/allClients',
                        dataType: 'json',
                        data: function(params) {
                            console.log(params);
                            return {
                                term: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.clients.map(function(frs) {
                                    return {
                                        id: frs.id,
                                        text: frs.nom_client
                                    };
                                })
                            };
                        }
                    }
                });
            });
        </script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script>
            $(document).ready(function() {
                $("#dateReglement").datepicker({
                    beforeShowDay: function(date) {
                        var currentDate = new Date();
                        currentDate.setHours(0, 0, 0, 0);
                        return [date <= currentDate];
                    },
                    dateFormat: 'dd-mm-yy' // Format de la date
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $("#connectBtn").on("click", function() {
                    $(".myLoader").show();
                    setTimeout(function() {
                        $(".myLoader").hide();
                    }, 2000);
                });
            });
        </script>
    </main>
@endsection
