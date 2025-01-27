<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use DOMDocument;
use HTMLtoOpenXML\Parser;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\TemplateProcessor;

final class WordService
{
	public function __construct(
		private readonly PlaceRepository $place_repository,
		private readonly LocationRepository $location_repository
	) {}

	public function get_data_sheet(Exhibit $exhibit)
	{
		$processedPath = storage_path('app/processed.docx'); // Pfad zu deinem verarbeiteten Dokument

		// Lade das Dokument mit TemplateProcessor
		$templateProcessor = new TemplateProcessor('Vorlage-Test.docx');

		$place = $this->place_repository->find($exhibit->get_place_id());
		$location = $this->location_repository->find($place->get_location_id());

		$templateProcessor->setValue('Name', $exhibit->get_name());
		$templateProcessor->setValue('Nummer', $exhibit->get_inventory_number());
		$templateProcessor->setValue('Hersteller', $exhibit->get_manufacturer());
		$templateProcessor->setValue('Herstellungsjahr', $exhibit->get_name());
		$templateProcessor->setValue('Standort', $location->get_name());
		$templateProcessor->setValue('Platz', $place->get_name());
		$templateProcessor->setValue('Anschaffungsdatum', $exhibit->get_aquiry_date() ?? '-');
		$templateProcessor->setValue('Aktueller Wert', $exhibit->get_current_value() . ' €');
		$templateProcessor->setValue('Ursprungswert', ($exhibit->get_original_price()?->get_amount() . ' ' . $exhibit->get_original_price()?->get_currency()?->get_code()) ?? '-');

		$replacers = [];

		foreach ($exhibit->get_free_texts() as $free_text) {
			$replacers[] = [
				'Titel' => $free_text->get_heading(),
				'Beschreibung' =>
				strip_tags($free_text->get_html())
			];
		};

		$templateProcessor->cloneBlock('block_Freitexte', 0, true, false, $replacers);


		// Speichere das verarbeitete Dokument
		$templateProcessor->saveAs($processedPath);

		// Schicke das verarbeitete Dokument als Response
		return response()->download($processedPath)->deleteFileAfterSend(true);
	}

	private function fix()
	{
		// Dein kaputtes HTML
		$brokenHtml = "<html><body><div><p>This is a <b>broken HTML";

		// Erstelle eine Instanz von DOMDocument
		$dom = new DOMDocument();

		// Lade das HTML und repariere es
		libxml_use_internal_errors(true);  // Verhindert Fehlerausgaben
		$dom->loadHTML($brokenHtml);

		// Repariertes HTML holen
		$repairedHtml = $dom->saveHTML();

		// Gib das reparierte HTML aus
		return $repairedHtml;
	}
}
