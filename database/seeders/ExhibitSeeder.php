<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enum\Currency;
use App\Models\Enum\KindOfAcquistion;
use App\Models\Enum\KindOfProperty;
use App\Models\Enum\Language;
use App\Models\Enum\PreservationState;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Models\Parts\AcquisitionInfo;
use App\Models\Parts\BookInfo;
use App\Models\Parts\DeviceInfo;
use App\Models\Parts\Price;
use App\Repository\ExhibitRepository;
use App\Repository\PlaceRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ExhibitSeeder extends Seeder
{
	/**
	 * @var string[]
	 */
	private const array MANUFACTURES = [
		'Acer',
		'Amiga',
		'Apple',
		'Asus',
		'Commodore International',
		'Compaq',
		'Dell',
		'Digital Equipment Corporation (DEC)',
		'Elektronika',
		'Fujitsu',
		'HP (Hewlett-Packard)',
		'IBM',
		'Lenovo',
		'MCC',
		'Microsoft',
		'NEC',
		'Nixdorf',
		'Nokia',
		'Proton',
		'RAE',
		'Razer',
		'Rubin',
		'Samsung',
		'Siemens',
		'Sord',
		'Thales',
		'Toshiba',
		'Unis',
		'VEB Elektronikwerke Leipzig',
		'VEB Gerätewerk „Falkenstein“',
		'VEB Hallesche Gerätewerke',
		'VEB Mikroelektronik „Karl Marx“',
		'VEB Robotron'
	];
	
	/**
	 * @var string[]
	 */
	private const array SOURCES = [
		'Ankauf Förderverein',
		'Ankauf Förderverein',
		'MBN — Museum hist. Bürotechnik Naunhof',
		'MBN — Museum hist. Bürotechnik Naunhof',
		'VEB Chemiewerk Nünchritz',
		'Comenius-Grundschule Chemnitz',
		'Norbert Waldheim',
		'Uwe Müller',
		'Karlheinz Schmidt',
		'Martha Röbenack'
	];
	
	/**
	 * @var string[]
	 */
	private const array AUTHORS = [
		'Donald Knuth',
		'Alan Turing',
		'Brian W. Kernighan',
		'Dennis Ritchie',
		'Grace Hopper',
		'Douglas Engelbart',
		'Richard Stallman',
		'Steve Wozniak',
		'Tim Berners-Lee',
		'Vint Cerf',
		'John von Neumann',
		'Andrew S. Tanenbaum',
		'Katherine Johnson',
		'Shafi Goldwasser',
		'Barbara Liskov',
		'Ada Lovelace',
		'Frances Allen'
	];
	
	/**
	 * @var Exhibit[]
	 */
	private array $exhibits = [];
	
	private readonly array $all_place_ids = [];
	
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly PlaceRepository $place_repository,
	) {
		$this->all_place_ids = array_map(static fn(Place $place): string => $place->get_id(), $this->place_repository->get_all());
	}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$all_exhibits = $this->exhibit_repository->get_all();
		foreach ($all_exhibits as $exhibit) {
			$this->exhibit_repository->remove($exhibit);
		}
		
		/*
		$this->create_exhibit(new Exhibit(
			inventory_number: 'N-12345',
			name: 'Nixdorf BA42',
			manufacturer: 'Diebold Nixdorf GmbH Paderborn',
			year_of_manufacture: 1961,
			place_id: $places[0]->get_id(),
			rubric_id: 'sonstiges',
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
			year_of_manufacture: 1962,
			place_id: $places[1]->get_id(),
			rubric_id: 'sonstiges',
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
			year_of_manufacture: 1963,
			place_id: $places[2]->get_id(),
			rubric_id: 'sonstiges',
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
			year_of_manufacture: 1964,
			place_id: $places[3]->get_id(),
			rubric_id: 'sonstiges',
			free_texts: [
				new FreeText(
					heading: "Geschichte",
					html: '<p>Der <strong>Nixdorf BA42</strong> war ein Computer, der in den frühen 1980er Jahren von der <strong>Nixdorf Computer AG</strong> (später Diebold Nixdorf GmbH) in Paderborn entwickelt wurde. Er wurde als Teil der <strong>BA-Serie</strong> konzipiert, die speziell für den Einsatz im <strong>Finanzwesen</strong> und in großen Unternehmen gedacht war. Der BA42 richtete sich vor allem an Banken und Finanzinstitute, die eine zuverlässige Lösung für die Verarbeitung von Finanztransaktionen und die Verwaltung von Geschäftsdaten benötigten.</p><p><br></p><p>Der BA42 zeichnete sich durch eine <strong>modulare Architektur</strong> aus, die es ermöglichte, den Computer flexibel an die spezifischen Bedürfnisse der Anwender anzupassen. Er unterstützte eine breite Palette von <strong>Ein- und Ausgabegeräten</strong> und konnte mit <strong>externem Speicher</strong> erweitert werden, um größere Datenmengen zu verarbeiten.</p><p><br></p><p>Mit seiner hohen <strong>Zuverlässigkeit</strong>, der <strong>sicheren Transaktionsverarbeitung</strong> und den speziell auf Finanzanwendungen ausgerichteten Funktionen war der BA42 ein wichtiger Bestandteil der frühen Computerisierung des Finanzwesens in den 1980er Jahren. Trotz des späteren Aufkommens leistungsfähigerer Systeme und der zunehmenden Verbreitung von Personalcomputern, blieb der BA42 für eine gewisse Zeit eine wichtige Lösung in der Finanzbranche.</p><p><br></p><p>Die Nixdorf Computer AG, die später mit Diebold fusionierte, spielte mit der Entwicklung des BA42 eine Schlüsselrolle bei der Etablierung von Computerlösungen für Banken und Finanzinstitute.</p>',
					is_public: true
				),
			],
		));
		*/

		for($i = 0; $i < 100; $i++) {
			$this->create_exhibit(new Exhibit(
				inventory_number: (string)$i,
				name: 'Exhibit '.$i,
				manufacturer: 'Standard',
				year_of_manufacture: 2000,
				place_id: $places[3]->get_id(),
				rubric_id: 'sonstiges',
				free_texts: [],
			));
		}
	}
	
	private function create_exhibit(
		string $inventory_number,
		string $name,
		?string $manufacturer = null,
		?int $year_of_manufacture  = null,
		?PreservationState $preservation_state = null,
		?Price $original_price = null,
		?int $current_value = null,
		?AcquisitionInfo $acquisition_info = null,
		?KindOfProperty $kind_of_property = null,
		?DeviceInfo $device_info = null,
		?BookInfo $book_info = null,
		?string $place_id = null,
		?array $connected_exhibit_ids = null,
		?array $free_texts = null,
	): void {
		if ($device_info) {
			$is_device = true;
			$book_info = null;
		} else {
			$is_device = false;
			$device_info = null;
		}
		
		$exhibit = new Exhibit(
			inventory_number: $inventory_number,
			name: $name,
			manufacturer: $manufacturer ?? fake()->randomElement(self::MANUFACTURES),
			year_of_manufacture: $year_of_manufacture ?? fake()->numberBetween(1930, Carbon::now()->year),
			preservation_state: $preservation_state ?? fake()->randomElement(PreservationState::cases()),
			original_price: $original_price ?? new Price(
				amount: fake()->numberBetween(100,5000000),
				currency: fake()->randomElement(Currency::cases())
			),
			current_value: $current_value ?? fake()->numberBetween(0,1000000),
			acquisition_info: $acquisition_info ?? new AcquisitionInfo(
				date: Carbon::create(fake()->dateTimeBetween(Carbon::parse('1930-01-01 00:00:00'))),
				source: fake()->randomElement(self::SOURCES),
				kind: fake()->randomElement(KindOfAcquistion::cases()),
				purchasing_price: fake()->numberBetween(1,1000000),
			),
			kind_of_property: $kind_of_property ?? fake()->randomElement(KindOfProperty::cases()),
			device_info: ($is_device ? ($device_info ?? new DeviceInfo(
				manufactured_from_date: $this->determinate_random_partial_date(),
				manufactured_to_date: $this->determinate_random_partial_date()
			)) : null),
			book_info: (!$is_device ? ($book_info ?? new BookInfo(
				authors: join(fake()->randomElements(self::AUTHORS, fake()->numberBetween(1,3)), '; '),
				isbn: fake()->isbn10(),
				language: fake()->randomElement(Language::cases()),
			)) : null),
			place_id: $place_id ?? fake()->randomElement($this->all_place_ids),
			connected_exhibit_ids: $connected_exhibit_ids ?? [],
			free_texts: $free_texts ?? [],
		);
		
		$this->exhibit_repository->insert($exhibit);
		$this->exhibits[] = $exhibit;
	}
	
	/**
	 * @return Exhibit[]
	 */
	public function get_exhibits(): array {
		return $this->exhibits;	
	}
	
	private function determinate_random_partial_date(): string {
		$time = fake()->dateTimeBetween(Carbon::parse('1930-01-01 00:00:00'));
		$variant = fake()->randomElement([0,1,2]);
		$str = $time->format('Y');
		if ($variant === 0) {
			return $str;
		}
		$str .= '-'.$time->format('m');
		if ($variant === 1) {
			return $str;
		}
		return $str . '-' . $time->format('d');
	}
}
