<?php namespace App\SUtils;
      
    
use App\ERP\SDocument;
use App\ERP\SDocumentRow;
use App\ERP\SDocumentRowTax;
use App\WMS\SStock;

class SDocumentsUtils {
    public static function fixDocuments($year) {
        $lYearsId = [
            '2016' => '1',
            '2017' => '2',
            '2018' => '3',
            '2019' => '4',
            '2020' => '5',
            '2021' => '6',
            '2022' => '7',
            '2023' => '8',
            '2024' => '9',
            '2025' => '10',
            '2026' => '11',
            '2027' => '12',
            '2028' => '13',
            '2029' => '14',
            '2030' => '15',
          ];

        // Documentos por external id
        $lDocuments = SDocument::where('year_id', $lYearsId[$year])
                                // ->where('external_id', '2023_1345')
                                ->orderBy('external_id', 'ASC')
                                ->distinct()
                                ->get()
                                ->keyBy('external_id');

        foreach ($lDocuments as $key => $oDocument) {
            // Obtener documentos con misma llave foránea
            $lSameKey = SDocument::where('external_id', $key)
                                    ->select('id_document')
                                    ->orderBy('id_document', 'ASC')
                                    ->get();

            if (count($lSameKey) > 1) {
                $firstWithRows = false;
                foreach ($lSameKey as $oDoc) {
                    // Obtener renglones del documento
                    $lRows = SDocumentRow::where('document_id', $oDoc->id_document)
                                            ->select('id_document_row')
                                            ->get();

                    // Si el documento no tiene renglones 
                    if (count($lRows) == 0) {
                        SDocument::find($oDoc->id_document)->delete();
                    }
                    else {
                        if (! $firstWithRows) {
                            $firstWithRows = true;
                            SDocumentsUtils::fixRows($oDoc->id_document);
                        }
                        else {
                            SDocumentsUtils::deleteDocument($oDoc->id_document);
                        }
                    }
                }
            }
            else {
                SDocumentsUtils::fixRows($oDocument->id_document);
            }
        }
    }

    private static function fixRows($idDoc) {
        $lRows = SDocumentRow::where('document_id', $idDoc)
                                ->where('is_deleted', false)
                                ->select('id_document_row', 'external_id')
                                ->orderBy('id_document_row', 'ASC')
                                ->orderBy('external_id', 'ASC')
                                ->get();

        $externals = [];
        foreach ($lRows as $oRow) {
            if (in_array($oRow->external_id, $externals)) {
                try {
                    SDocumentRow::where('id_document_row', $oRow->id_document_row)->update(['is_deleted' => true]);

                    // SDocumentRowTax::where('document_row_id', $oRow->id_document_row)
                    //                             ->update(['is_deleted' => true]);
                }
                catch (\Throwable $th) {
                    \Log::error($th);
                }
            }
            else {
                $externals[] = $oRow->external_id;
            }
        }
    }

    private static function deleteDocument($idDoc) {
        $baseQuery = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('erpu_documents AS d')
                        ->join('erpu_document_rows as dr', 'd.id_document', '=', 'dr.document_id')
                        ->join('erpu_doc_row_taxes as drt', 'dr.id_document_row', '=', 'drt.document_row_id')
                        ->where('id_document', $idDoc);

        $aRows = $baseQuery->select('id_document_row')->lists('id_document_row');


        $lStock = SStock::where(function ($query) use ($aRows) {
                                $query->whereIn('doc_order_row_id', $aRows)
                                    ->orWhereIn('doc_invoice_row_id', $aRows)
                                    ->orWhereIn('doc_debit_note_row_id', $aRows)
                                    ->orWhereIn('doc_credit_note_row_id', $aRows);
                            })->select('id_stock')->lists('id_stock');

        if (count($lStock) == 0) {
            // Delete tax rows
            $aTaxes = $baseQuery->select('id_row_tax')->lists('id_row_tax');
            SDocumentRowTax::whereIn('id_row_tax', $aTaxes)->delete();

            // Delete rows
            SDocumentRow::whereIn('id_document_row', $aRows)->delete();

            // Delete document
            SDocument::find($idDoc)->delete();
        }
        else {
            \Log::alert("Documento con referencias: ".$idDoc);
        }
    }
}