<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\ExhibitRepository;
use Illuminate\Database\Seeder;

class ExhibitSeeder extends Seeder
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository
	) {}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$all_exhibits = $this->exhibit_repository->get_all();
		foreach ($all_exhibits as $exhibit) {
			$this->exhibit_repository->remove($exhibit);
		}
		
		$this->create_exhibit(new Exhibit(
			inventory_number: 'N-12345',
			name: 'Nixdorf BA42',
			manufacturer: 'Diebold Nixdorf GmbH Paderborn',
			free_texts: [
				new FreeText(
					heading: "Geschichte",
					html: '<p>Nixdorf Computer AG) in Paderborn, Deutschland, entwickelt wurde. Er wurde in den frühen 1980er Jahren als Business- und Finanzcomputer vorgestellt, vor allem für den Einsatz in Banken und Unternehmen, die eine zuverlässige Rechnungs- und Transaktionsverarbeitung benötigten. Der BA42 war Teil der Nixdorf BA-Serie, die als eine der ersten Systeme galt, die speziell für den Einsatz im Banken- und Finanzwesen konzipiert wurden.</p><p><br></p><p>Der BA42 zeichnete sich durch seine robuste Architektur und die hohe Verfügbarkeit aus, was ihn zu einer wichtigen Lösung für die Anforderungen der Finanzbranche machte. Er unterstützte maßgeschneiderte Softwarelösungen und war für seine Zeit leistungsfähig, besonders im Hinblick auf die Transaktionsverarbeitung und das Datenmanagement. Mit der zunehmenden Verbreitung personaler Computer und modernerer Systeme wurde der BA42 jedoch nach und nach durch neuere Technologien ersetzt.</p><p><br></p><p>Die Diebold Nixdorf GmbH, die heute für ihre Lösungen im Bereich Bank- und Finanzdienstleistungen bekannt ist, wurde durch die Entwicklungen wie den BA42 zu einem wichtigen Anbieter von Geschäftsinformatiklösungen.</p>',
					is_public: true
				),
				new FreeText(
					heading: "TODOs",
					html: '<ol><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Wackelkontakt Seite</li><li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>Blende locker</li></ol>',
					is_public: false
				),
				]
			)
		);
		$this->create_exhibit(new Exhibit(
			inventory_number: 'T-12345', 
			name: 'Tiumphator CRN1',
			manufacturer: 'Triumphator Leipzig (Mölkau) DDR',
			free_texts: [
				new FreeText(
					heading: "Geschichte",
					html: '<p>Der <strong>Triumphator CRN1</strong> war ein frühes Computersystem, das in der DDR von der Firma <strong>Triumphator</strong> in Leipzig (Mölkau) entwickelt wurde. Er entstand in den 1960er Jahren und war vor allem für betriebliche und wissenschaftliche Anwendungen konzipiert. Triumphator, ein Unternehmen, das ursprünglich Schreibmaschinen und Bürogeräte produzierte, begann mit der Entwicklung von Computern, um den wachsenden Bedarf an Datenverarbeitung in der DDR zu decken.</p><p><br></p><p>Der <strong>CRN1</strong> war ein transistorbasierter Rechner, der als Nachfolger des frühen Röhrencomputers "Triumphator 300" entwickelt wurde. Er wurde in verschiedenen Institutionen, wie Universitäten und Forschungszentren, eingesetzt und war vor allem für Rechenaufgaben in der Industrie und für wissenschaftliche Berechnungen gedacht. Der Triumphator CRN1 war in seiner Zeit für die DDR eine wichtige Technologie, da er eine der wenigen lokalen Entwicklungen im Bereich der Computertechnik darstellte.</p><p><br></p><p>Trotz seiner Bedeutung für die DDR blieb der CRN1 im internationalen Vergleich technisch hinter den westlichen Systemen zurück und konnte sich auf dem Markt nicht durchsetzen. Mit der Zeit wurde er durch leistungsfähigere Computer ersetzt, und die Firma Triumphator stellte ihre Computertätigkeit ein. Trotzdem bleibt der Triumphator CRN1 ein bedeutendes Beispiel für die Computergeschichte der DDR.</p>',
					is_public: true
				),
				new FreeText(
					heading: "Provenienz",
					html: '<p>Der Triumphator CRN1 in unserem Computermuseum wurde 1969 von der Leipziger Firma Triumphator als Teil eines innovativen Projekts für wissenschaftliche und industrielle Anwendungen entwickelt. Dieser Rechner wurde ursprünglich an die Universität Leipzig ausgeliefert, wo er für komplexe Berechnungen in der Ingenieurwissenschaft und Mathematik genutzt wurde. In den späten 1970er Jahren gelangte der CRN1 in den Besitz eines großen Forschungsinstituts in der DDR, das ihn bis zur Wendezeit in Betrieb hatte. Nach der Wiedervereinigung wurde der Rechner von einem privaten Sammler aufgekauft, der ihn schließlich an unser Museum übergab, um ein Stück Computergeschichte der DDR zu bewahren.</p>',
					is_public: true
				),
			]
		));
		$this->create_exhibit(new Exhibit(
			inventory_number: 'N-98765',
			name: 'Nixdorf 8810 M55',
			manufacturer: 'Nixdorf Computer AG Paderborn',
			free_texts: [
				new FreeText(
					heading: "Geschichte",
					html: '<p>Der <strong>Nixdorf 8810 M55</strong> war ein Minicomputer, der in den 1970er Jahren von der <strong>Nixdorf Computer AG</strong> in Paderborn entwickelt wurde. Er gehörte zur <strong>Nixdorf 8800-Serie</strong>, die eine der ersten leistungsfähigen Systeme für die kommerzielle Nutzung in Deutschland darstellte. Die Nixdorf 8810 M55 war besonders in den Bereichen Buchhaltung, Lagerverwaltung und für betriebliche Anwendungen weit verbreitet und wurde vor allem von mittelständischen Unternehmen genutzt.</p><p>Der Nixdorf 8810 M55 zeichnete sich durch eine modulare Architektur aus, die eine einfache Erweiterung und Anpassung an verschiedene Anforderungen ermöglichte. Mit einer Verarbeitungseinheit auf Transistorbasis und einer hohen Speicherkapazität für die damalige Zeit war er ein wichtiger Schritt in der Entwicklung von Computern für den Business- und Industriebereich.</p><p><br></p><p>Die Nixdorf 8810 M55 unterstützte sowohl die Verarbeitung von numerischen Daten als auch einfache grafische Anwendungen. Sie war ein wichtiger Bestandteil in der Computerisierung von Geschäftsprozessen und spielte eine Schlüsselrolle bei der Einführung von Computern in mittelständischen Unternehmen in Deutschland und darüber hinaus.</p><p><br></p><p>Nach der Einführung von leistungsfähigeren Systemen und der zunehmenden Verbreitung von Personalcomputern in den 1980er Jahren wurde der Nixdorf 8810 M55 nach und nach durch neuere Modelle ersetzt. Dennoch bleibt er ein wichtiger Teil der Computergeschichte, da er zur breiteren Akzeptanz von Computern in der Wirtschaft beitrug.</p>',
					is_public: true
				),
				new FreeText(
					heading: "Funktionsweise",
					html: '<p>Der <strong>Nixdorf 8810 M55</strong> war ein Minicomputer aus den 1970er Jahren, der für betriebliche Anwendungen in kleinen und mittelständischen Unternehmen entwickelt wurde. Er verfügte über eine <strong>8-Bit-CPU</strong> mit einer Taktrate von 1 MHz und nutzte <strong>Magnetkernspeicher</strong> mit einer Kapazität von bis zu 32 KB. Der Computer hatte eine <strong>modulare Architektur</strong>, die Erweiterungen wie zusätzliche Speicher und Ein-/Ausgabegeräte ermöglichte. Er unterstützte verschiedene <strong>Ein-/Ausgabegeräte</strong> wie Terminals und Drucker. Der Nixdorf 8810 M55 wurde hauptsächlich für Anwendungen wie Buchhaltung und Lagerverwaltung eingesetzt und zeichnete sich durch seine Flexibilität und Erweiterbarkeit aus.</p>',
					is_public: true
				),
			],
		));
		$this->create_exhibit(new Exhibit(
			inventory_number: 'NB-42',
			name: 'Nixdorf BA42',
			manufacturer: 'Diebold Nixdorf GmbH Paderborn',
			free_texts: [
				new FreeText(
					heading: "Geschichte",
					html: '<p>Der <strong>Nixdorf BA42</strong> war ein Computer, der in den frühen 1980er Jahren von der <strong>Nixdorf Computer AG</strong> (später Diebold Nixdorf GmbH) in Paderborn entwickelt wurde. Er wurde als Teil der <strong>BA-Serie</strong> konzipiert, die speziell für den Einsatz im <strong>Finanzwesen</strong> und in großen Unternehmen gedacht war. Der BA42 richtete sich vor allem an Banken und Finanzinstitute, die eine zuverlässige Lösung für die Verarbeitung von Finanztransaktionen und die Verwaltung von Geschäftsdaten benötigten.</p><p><br></p><p>Der BA42 zeichnete sich durch eine <strong>modulare Architektur</strong> aus, die es ermöglichte, den Computer flexibel an die spezifischen Bedürfnisse der Anwender anzupassen. Er unterstützte eine breite Palette von <strong>Ein- und Ausgabegeräten</strong> und konnte mit <strong>externem Speicher</strong> erweitert werden, um größere Datenmengen zu verarbeiten.</p><p><br></p><p>Mit seiner hohen <strong>Zuverlässigkeit</strong>, der <strong>sicheren Transaktionsverarbeitung</strong> und den speziell auf Finanzanwendungen ausgerichteten Funktionen war der BA42 ein wichtiger Bestandteil der frühen Computerisierung des Finanzwesens in den 1980er Jahren. Trotz des späteren Aufkommens leistungsfähigerer Systeme und der zunehmenden Verbreitung von Personalcomputern, blieb der BA42 für eine gewisse Zeit eine wichtige Lösung in der Finanzbranche.</p><p><br></p><p>Die Nixdorf Computer AG, die später mit Diebold fusionierte, spielte mit der Entwicklung des BA42 eine Schlüsselrolle bei der Etablierung von Computerlösungen für Banken und Finanzinstitute.</p>',
					is_public: true
				),
			],
		));
	}
	
	private function create_exhibit(Exhibit $exhibit): void {
		$this->exhibit_repository->insert($exhibit);
	}
}
