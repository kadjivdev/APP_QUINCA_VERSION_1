@extends('layout.template')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Ventes au comptant</h1>
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
                                <h5 class="card-title">Enregistrer une vente</h5>
                            </div>
                        </div>
                        <form class="row g-3" action="{{ route('ventes.update', $vente->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="col-5 mb-3">
                                <label class="form-label">Client</label>
                                <select name="client_id" class="js-example-basic-single form-control" id="client_id" >
                                    <option value=""> </option>
                                    @foreach ($clients as $client)
                                        <option {{ $vente->client_id == $client->id ? 'selected' : '' }} value="{{$client->id}}">{{$client->nom_client}}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="client_facture" value="{{$client}}" readonly id="client_facture" class="form-control"> --}}
                            </div>

                            <div class="col-3">
                                <label class="form-label">Date</label>
                                <input type="date" required name="date_fact" id="data_fact" value="{{ \Carbon\Carbon::parse($vente->date_vente)->format('Y-m-d') }}"  class="form-control">
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Type de facture</label>
                                <select class="form-select" required name="type_id" id="typeSelect">
                                    <option value="">Choisir le type </option>

                                    @foreach ($types as $type)
                                    <option  value="{{ $type->id }}" {{ $data_vente->facture_type_id == $type->id ? 'selected' : '' }}> {{ $type->libelle }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Type de vente</label>
                                <select class="form-select" required name="type_vente_id" id="typeVenteSelect">
                                    <option value="">Choisir le type </option>

                                    @foreach ($typeVentes as $typeVente)
                                    <option value="{{ $typeVente->id }}" {{ $vente->type_vente_id == $typeVente->id ? 'selected' : '' }} data-donnee="{{ $typeVente->libelle }}">
                                        {{ $typeVente->libelle }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-label">Choisir l'article</label>
                                    <select class="form-select form-control test" name="article_id" id="articleSelect">
                                        <option value="">Choisir l'article </option>
                                        @foreach ($articles as $article)
                                        <option data-qteDispo="{{ $article->qte_stock }}" data-prixVente="{{ $article->prix_special }}" data-prixBtp="{{ $article->prix_btp }}" data-prixRevendeur="{{ $article->prix_revendeur }}" data-prixParticulier="{{ $article->prix_particulier }}" value="{{ $article->id }}">
                                            {{ $article->nom }}
                                            ({{ $article->qte_stock }})</span>
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" name="qte" id="qte" class="form-control">
                                </div>

                                <div class="col-2">
                                    <label class="form-label">Prix unitaire</label>
                                    <input type="number" name="prix" id="prix" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="form-label">Unité</label>
                                    <select class="form-select" name="unite_id" id="uniteSelect">
                                        <option value="">Choisir l'unité </option>
                                    </select>
                                </div>

                                <div class="col-2 py-2">
                                    <button class="btn btn-primary mt-4" type="button" id="ajouterArticle">
                                        Ajouter</button>
                                </div>
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
                                            <th>Action</th>
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
                                                <td>{{$ligne->nom}}<input type="hidden" required name="articles[]" value="{{ $ligne->article_id }}"></td>
                                                <td>{{$ligne->qte_cmde}} <input type="hidden" required name="qte_cdes[]" value="{{ $ligne->qte_cmde }}"></td>
                                                <td>{{$ligne->prix_unit}} <input type="hidden" required name="prixUnits[]" value="{{ $ligne->prix_unit }}"></td>
                                                <td>{{$ligne->unite}} <input type="hidden" required name="unites[]" value="{{ $ligne->unite_mesure_id }}"></td>
                                                <td>{{$ligne->prix_unit * $ligne->qte_cmde}} <input type="hidden" required name="montants[]" value="{{$ligne->prix_unit * $ligne->qte_cmde}}"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm delete-row">Supprimer</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">Montant HT</td>
                                            <td colspan="3" style="background-color: rgba(233, 138, 10, 0.89);"><input type="text" id="totalInput" class="form-control" name="montant_facture" readonly>
                                            </td>
                                        </tr>
                                        <tr id="rowRem">
                                            <td colspan="2">Taux remise</td>
                                            <td colspan="3" style="background-color: rgba(245, 39, 54, 0.8);"><input type="text" id="tauxRemise" class="form-control" name="taux_remise">
                                            </td>
                                        </tr>
                                        <tr id="rowAib" style="background-color: red !important;">
                                            <td colspan="2">Taux AIB (%)</td>
                                            <td colspan="3" style="background-color: rgba(114, 93, 228, 0.89);"><input type="text" id="aib" value="{{ old('aib') }}" class="form-control" name="aib">
                                                <input type="text" id="montant_aib" class="form-control" readonly>
                                            </td>
                                        </tr>
                                        <tr id="rowTva">
                                            <td colspan="2">TVA(%)</td>
                                            <td colspan="3" style="background-color: rgba(150, 150, 150, 0.89);"><input type="number" id="tva" min="0" max="18" value="{{ old('tva') }}" class="form-control" name="tva">
                                                <input type="text" id="montant_tva" class="form-control" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Montant total</td>
                                            <td colspan="3" style="background-color: rgba(233, 138, 10, 0.89);"><input type="text" id="totalNet" class="form-control" name="montant_total" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Montant payer</td>
                                            <td colspan="3" style="background-color: rgba(32, 214, 4, 0.89);"><input type="number" id="montant_regle" class="form-control" name="montant_regle" required>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Modifier</button>
                                <button type="reset" class="btn btn-secondary">Annuler</button>
                            </div>
                        </form>
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


        $('.js-client-select').select2({
            placeholder: 'Selectionner client',
            minimumInputLength: 2,
            allowAdd: true,
            ajax: {
                url: apiUrl + '/cltListAjax',
                dataType: 'json',
                data: function(params) {
                    console.log(params);
                    return {
                        term: params.term // search term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.clients.map(function(clt) {
                            return {
                                id: clt.id,
                                text: clt.nom_client
                            };
                        })
                    };
                }
            },

        });
    });

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

        var typeFacture = $("#typeSelect").val();

        if (typeFacture == 2) {
            $('#rowRem').hide();
            $('#rowAib').hide();
            $('#rowTva').hide();
        }

        if (typeFacture == 1) {
            $('#rowRem').show();
            $('#rowAib').show();
            $('#rowTva').show();
        }

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
            var tauxRemise = parseFloat($('#tauxRemise').val()) || 0;
            var tauxAIB = parseFloat($('#aib').val()) || 0;
            var tauxTVA = parseFloat($('#tva').val()) || 0;

            var totalAvecRemise = total * (1 - tauxRemise / 100);
            var totalAIB = totalAvecRemise * (tauxAIB / 100);
            var totalTVA = totalAvecRemise * (tauxTVA / 100);
            var totalNet = totalAvecRemise * (1 + (tauxAIB / 100) + (tauxTVA / 100));
            $('#totalNet').val(totalNet.toFixed(2));
            $('#montant_tva').val(totalTVA.toFixed(2));
            $('#montant_aib').val(totalAIB.toFixed(2));

            $('#totalInput').val(total.toFixed(2));
            $('#montant_regle').val(totalNet.toFixed(2));
            $('#montant_regle').attr('min', totalNet.toFixed(2));
            $('#montant_regle').attr('max', totalNet.toFixed(2));
        }

        $('#articleSelect').select2({
            width: 'resolve'
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

        $('#typeVenteSelect').on('change', function() {
            var typeVente = $('#typeVenteSelect option:selected').attr('data-donnee');
            var prixMin;
            var prix = $('#prix').val();

            if (typeVente === 'BTP') {
                prixMin = $('#articleSelect option:selected').attr('data-prixBtp');
            } else if (typeVente === 'Revendeur') {
                prixMin = $('#articleSelect option:selected').attr('data-prixRevendeur');
            } else if (typeVente === 'Particulier') {
                prixMin = $('#articleSelect option:selected').attr('data-prixParticulier');
            } else {
                prixMin = $('#articleSelect option:selected').attr('data-prixVente');
            }

            $('#prix').attr('min', prixMin);

            console.log(prix, prixMin, 'les prix');
        });

        // $('#ajouterArticle').click(function() {
        //     var quantiteConvertiePromise = convertirQuantite();

        //     quantiteConvertiePromise.done(function(data) {
        //         var quantiteConvertie = parseFloat(data.qteConvertie);

        //         console.log("first", quantiteConvertie);

        //         var articleId = $('#articleSelect').val();
        //         var articleNom = $('#articleSelect option:selected').text();
        //         var uniteId = $('#uniteSelect option:selected').val();
        //         var uniteNom = $('#uniteSelect option:selected').text();
        //         var quantite = $('#qte').val();
        //         var prix = $('#prix').val();
        //         var total = prix * quantite;
        //         var qteStock = $('#articleSelect option:selected').attr('data-qteDispo');
        //         // var prixMin = $('#articleSelect option:selected').attr('data-prixVente');

        //         var typeVente = $('#typeVenteSelect option:selected').attr('data-donnee');
        //         var prixMin;

        //         if (typeVente === 'BTP') {
        //             prixMin = $('#articleSelect option:selected').attr('data-prixBtp');
        //         } else if (typeVente === 'Revendeur') {
        //             prixMin = $('#articleSelect option:selected').attr('data-prixRevendeur');
        //         } else if (typeVente === 'Particulier') {
        //             prixMin = $('#articleSelect option:selected').attr('data-prixParticulier');
        //         } else {
        //             prixMin = $('#articleSelect option:selected').attr('data-prixVente');
        //         }

        //         $('#prix').attr('min', prixMin);

        //         if (parseFloat(prix) < prixMin) {
        //             alert('Le prix unitaire est inférieur au prix minimum (' + prixMin + ').');
        //             return; // Bloquer l'ajout si le prix est inférieur au prix minimal
        //         }

        //         console.log(prixMin, 'prix minim', total);

        //         if (quantiteConvertie > parseFloat(qteStock)) {
        //             alert('La quantité disponible est insuffisante.');
        //             return;
        //         }

        //         var newRow = `
        //                             <tr>
        //                                 <td>${articleNom}<input type="hidden" required name="articles[]" value="${articleId}">
        //                                     </td>
        //                                 <td data-name="qte_cmd">${quantite} <input type="hidden" required name="qte_cdes[]" value="${quantite}"</td>
        //                                 <td data-name="prix_unit">${prix} <input type="hidden" required name="prixUnits[]" value="${prix}"</td>
        //                                 <td>${uniteNom} <input type="hidden" required name="unites[]" value="${uniteId}"</td>
        //                                 <td data-name="montant"  contenteditable="false">${total}
        //                                     <input type="hidden" name="montants[]" value="${total}" readonly class="form-control">
        //                                 </td>
        //                                 <td><button type="button" class="btn btn-danger btn-sm delete-row">Supprimer</button></td>
        //                             </tr>`;

        //         $('#editableTable tbody').append(newRow);
        //         calculateTotal();

        //         // Effacer les champs après l'ajout
        //         $('#articleSelect').val(null).trigger('change');
        //         $('#uniteSelect').val('');
        //         $('#prix').val('');
        //         $('#qte').val('');
        //     });

        //     quantiteConvertiePromise.fail(function(error) {
        //         console.log('Erreur de la requête Ajax :', error);
        //     });
        //     $('#enregistrerVente').click(function() {
        //         $('#venteForm').submit();
        //     });

        //     $('#editableTable').on('click', '.delete-row', function() {
        //         $(this).closest('tr').remove();
        //         calculateTotal();

        //     });

        // });

        // function convertirQuantite() {
        //     var articleId = $('#articleSelect').val();
        //     var uniteId = $('#uniteSelect').val();
        //     var quantite = $('#qte').val();

        //     if (articleId && uniteId && quantite) {
        //         return $.ajax({
        //             url: apiUrl + '/convertirUnite/' + articleId + '/' + uniteId + '/' + quantite,
        //             type: 'GET',
        //         });
        //     }
        //     return $.Deferred().reject().promise();

        // }

        // $('#ajouterArticle').click(function() {
        //     var quantiteConvertiePromise = convertirQuantite();

        //     quantiteConvertiePromise.done(function(data) {
        //         var quantiteConvertie = parseFloat(data.qteConvertie);

        //         console.log("first", quantiteConvertie);

        //         var articleId = $('#articleSelect').val();
        //         var articleNom = $('#articleSelect option:selected').text();
        //         var uniteId = $('#uniteSelect').val();
        //         var uniteNom = $('#uniteSelect option:selected').text();
        //         var quantite = $('#qte').val();
        //         var prix = $('#prix').val();
        //         var total = prix * quantite;
        //         var qteStock = $('#articleSelect option:selected').data('qteDispo');

        //         var typeVente = $('#typeVenteSelect option:selected').data('donnee');
        //         var prixMin = $('#articleSelect option:selected').data('prixVente');

        //         if (typeVente === 'BTP') {
        //             prixMin = $('#articleSelect option:selected').data('prixBtp');
        //         } else if (typeVente === 'Revendeur') {
        //             prixMin = $('#articleSelect option:selected').data('prixRevendeur');
        //         } else if (typeVente === 'Particulier') {
        //             prixMin = $('#articleSelect option:selected').data('prixParticulier');
        //         }

        //         $('#prix').attr('min', prixMin);

        //         if (parseFloat(prix) < prixMin) {
        //             alert('Le prix unitaire est inférieur au prix minimum (' + prixMin + ').');
        //             return; // Bloquer l'ajout si le prix est inférieur au prix minimal
        //         }

        //         console.log(prixMin, 'prix minim', total);

        //         if (quantiteConvertie > parseFloat(qteStock)) {
        //             alert('La quantité disponible est insuffisante.');
        //             return;
        //         }

        //         var newRow = `
        //     <tr>
        //         <td>${articleNom}<input type="hidden" required name="articles[]" value="${articleId}">
        //         </td>
        //         <td data-name="qte_cmd">${quantite} <input type="hidden" required name="qte_cdes[]" value="${quantite}"></td>
        //         <td data-name="prix_unit">${prix} <input type="hidden" required name="prixUnits[]" value="${prix}"></td>
        //         <td>${uniteNom} <input type="hidden" required name="unites[]" value="${uniteId}"></td>
        //         <td data-name="montant"  contenteditable="false">${total}
        //             <input type="hidden" name="montants[]" value="${total}" readonly class="form-control">
        //         </td>
        //         <td><button type="button" class="btn btn-danger btn-sm delete-row">Supprimer</button></td>
        //     </tr>`;

        //         $('#editableTable tbody').append(newRow);
        //         calculateTotal();

        //         // Effacer les champs après l'ajout
        //         $('#articleSelect').val(null).trigger('change');
        //         $('#uniteSelect').val('');
        //         $('#prix').val('');
        //         $('#qte').val('');
        //     });

        //     quantiteConvertiePromise.fail(function(error) {
        //         console.log('Erreur de la requête Ajax :', error);
        //     });
        // });

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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    $(".js-example-basic-single").select2();
</script>

@endsection
