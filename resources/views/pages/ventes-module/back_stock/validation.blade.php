@extends('layout.template')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Retour de stock</h1>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="card-title">Valider le retour de stock {{$back->reference}}</h5>
                            </div>
                        </div>
                        <form class="row g-3" action="{{ route('back_stock.update', $back->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="col-3 mb-3">
                                <label class="form-label">Provenance</label>
                                <select class="form-control" required name="magasin_from_id" id="magasin_from_id">
                                    @foreach ($magasins as $magasin)
                                        @if ($back->from_magasin_id == $magasin->id)
                                            <option value="{{$magasin->id}}">{{$magasin->nom}}</option>                                            
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3 mb-3">
                                <label class="form-label">Destination</label>
                                <select class="form-control" required name="magasin_id" id="magasin_id">
                                    @foreach ($magasins as $magasin)
                                        @if ($back->to_magasin_id == $magasin->id)
                                            <option value="{{$magasin->id}}">{{$magasin->nom}}</option>                                            
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <label class="form-label">Client</label>
                                <select class="form-control" required name="client_id" id="client_id">
                                    @foreach ($clients as $client)
                                        @if ($back->client_id == $client->id)
                                            <option value="{{$client->id}}">{{$client->nom_client}}</option>                                  
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <label class="form-label">Date</label>
                                <input type="date" required name="date_back" value="{{Carbon\Carbon::parse($back->date_op)->format('Y-m-d')}}" id="date_back" class="form-control">
                            </div>

                            <div id="dynamic-fields-container">
                                <table id="editableTable" class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Quantité</th>
                                            <th>Prix unit</th>
                                            <th>Unité mesure</th>
                                            <th>Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach ($lignes as $ligne )
                                            @php
                                                $total += ($ligne->prix_unit * $ligne->qte_cmde)
                                            @endphp
                                            <tr>
                                                <td>{{$ligne->article->nom}}<input type="hidden" required name="articles[]" value="{{ $ligne->article_id }}"></td>
                                                <td>{{$ligne->qte_back}} <input type="hidden" required name="qte_cdes[]" value="{{ $ligne->qte_back }}"></td>
                                                <td>{{$ligne->prix_unit}} <input type="hidden" required name="prixUnits[]" value="{{ $ligne->prix_unit }}"></td>
                                                <td>{{$ligne->unite_mesure->unite}} <input type="hidden" required name="unites[]" value="{{ $ligne->unite_mesure_id }}"></td>
                                                <td>{{$ligne->prix_unit * $ligne->qte_back}} <input type="hidden" required name="montants[]" value="{{$ligne->prix_unit * $ligne->qte_back}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <div>
                            @if (is_null($back->validate_at))
                                <form action="{{ route('back-validate', $back->id) }}"
                                    method="POST" class="col-3">
                                    @csrf
                                    @method('GET')
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Voulez vous vraiment valider ce retour de stock? Cette opération est irréversible')">Valider le retour</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
</main>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<script src="{{ asset('assets/js/jquery3.6.min.js') }}"></script>
<script src="{{ asset('assets/js/mindmup-editabletable.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    var apiUrl = "{{ config('app.url_ajax') }}";

    $(document).ready(function() {
        $('#clientForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('clients.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#clientModal').modal('hide');
                    } else {
                        console.error('Error creating client:', response.message);
                    }
                }
            });
        });
    });
</script>
<script>
    var apiUrl = "{{ config('app.url_ajax') }}";

    $(document).ready(function() {

        $('#editableTable tbody').on('input', 'input[name^="qte_cdes"],  input[name^="prixUnits"]', function() {
            calculateTotal();
        });
        $('#editableTable tfoot').on('input', '#tauxRemise, #aib, #tva', function() {
            calculateTotal();
        });


        $('#tauxRemise').val(0);
        $('#aib').val(0);
        $('#tva').val(0);
        calculateTotal();
        $('#typeSelect').change(function() {


            var selectedOption = $(this).val(); // Obtient la valeur sélectionnée

            if (selectedOption == 2) {
                $('#rowRem').hide();
                $('#rowAib').hide();
                $('#rowTva').hide();
            }

            if (selectedOption == 1) {
                $('#rowRem').show();
                $('#rowAib').show();
                $('#rowTva').show();
            }


        });

        function calculateTotal() {
            var total = 0;
            $('#editableTable tbody tr').each(function() {
                var qte_cmde = parseFloat($(this).find('input[name^="qte_cdes"]').val()) || 0;
                var prix_unit = parseFloat($(this).find('input[name^="prixUnits"]').val()) || 0;

                total += qte_cmde * prix_unit;
            });
            
            $('#montant_total').val(total.toFixed(2));
        }

        $('#articleSelect').select2({
            width: 'resolve'
        });

        $('#magasin_id').select2({
            
        });

        $('#magasin_from_id').select2({
            
        });

        $('#articleSelect').on('change', function() {
            var articleId = $(this).val();
            console.log(articleId, 'id article');
            if (articleId) {
                $.ajax({
                    url: apiUrl + '/getUnitesByArticle/' + articleId,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        var options = '<option value="">Choisir l\'unité</option>';
                        for (var i = 0; i < data.unites.length; i++) {
                            options += '<option value="' + data.unites[i].id + '">' + data
                                .unites[i].unite + '</option>';
                        }
                        $('#uniteSelect').html(options);
                    },
                    error: function(error) {
                        console.log('Erreur de la requête Ajax :', error);
                    }
                });
            } else {
                $('#uniteSelect').html('<option value="">Choisir l\'unité</option>');
            }
        });

        $('#editableTable').editableTableWidget();

        $('#uniteSelect').on('change', function() {
            convertirQuantite();
        });
        

        $('#ajouterArticle').click(function() {
        var quantiteConvertiePromise = convertirQuantite();

        quantiteConvertiePromise.done(function(data) {
        var quantiteConvertie = parseFloat(data.qteConvertie);

        console.log("first", quantiteConvertie);

        var articleId = $('#articleSelect').val();

        // Vérifier si l'article existe déjà dans le tableau
        var existingArticle = $('#editableTable tbody tr').find('input[name="articles[]"][value="' + articleId + '"]');
        if (existingArticle.length > 0) {
            alert('Cet article a déjà été ajouté.');
            return;
        }

        var articleNom = $('#articleSelect option:selected').text();
        var uniteId = $('#uniteSelect').val();
        var uniteNom = $('#uniteSelect option:selected').text();
        var quantite = $('#qte').val();
        var prix = $('#prix').val();
        var total = prix * quantite;
        var qteStock = $('#articleSelect option:selected').data('qteDispo');

        var typeVente = $('#typeVenteSelect option:selected').data('donnee');
        var prixMin = $('#articleSelect option:selected').data('prixVente');

        if (typeVente === 'BTP') {
            prixMin = $('#articleSelect option:selected').data('prixBtp');
        } else if (typeVente === 'Revendeur') {
            prixMin = $('#articleSelect option:selected').data('prixRevendeur');
        } else if (typeVente === 'Particulier') {
            prixMin = $('#articleSelect option:selected').data('prixParticulier');
        }

        $('#prix').attr('min', prixMin);

        if (parseFloat(prix) < prixMin) {
            alert('Le prix unitaire est inférieur au prix minimum (' + prixMin + ').');
            return; // Bloquer l'ajout si le prix est inférieur au prix minimal
        }

        console.log(prixMin, 'prix minim', total);

        if (quantiteConvertie > parseFloat(qteStock)) {
            alert('La quantité disponible est insuffisante.');
            return;
        }

        var newRow = `
            <tr>
                <td>${articleNom}<input type="hidden" required name="articles[]" value="${articleId}">
                </td>
                <td data-name="qte_cmd">${quantite} <input type="hidden" required name="qte_cdes[]" value="${quantite}"></td>
                <td data-name="prix_unit">${prix} <input type="hidden" required name="prixUnits[]" value="${prix}"></td>
                <td>${uniteNom} <input type="hidden" required name="unites[]" value="${uniteId}"></td>
                <td data-name="montant"  contenteditable="false">${total}
                    <input type="hidden" name="montants[]" value="${total}" readonly class="form-control">
                </td>
                <td><button type="button" class="btn btn-danger btn-sm delete-row">Supprimer</button></td>
            </tr>`;

        $('#editableTable tbody').append(newRow);
        calculateTotal();

        // Effacer les champs après l'ajout
        $('#articleSelect').val(null).trigger('change');
        $('#uniteSelect').val('');
        $('#prix').val('');
        $('#qte').val('');
    });

    quantiteConvertiePromise.fail(function(error) {
        console.log('Erreur de la requête Ajax :', error);
    });
});


        $('#enregistrerVente').click(function() {
            $('#venteForm').submit();
        });

        $('#editableTable').on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        function convertirQuantite() {
            var articleId = $('#articleSelect').val();
            var uniteId = $('#uniteSelect').val();
            var quantite = $('#qte').val();

            if (articleId && uniteId && quantite) {
                return $.ajax({
                    url: apiUrl + '/convertirUnite/' + articleId + '/' + uniteId + '/' + quantite,
                    type: 'GET',
                });
            }
            return $.Deferred().reject().promise();
        }

    });
</script>

@endsection
