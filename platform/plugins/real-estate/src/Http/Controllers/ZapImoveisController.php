<?php

namespace Srapid\RealEstate\Http\Controllers;

use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Repositories\Interfaces\PropertyInterface;
use Srapid\RealEstate\Enums\PropertyTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use DateTime;
use Exception;
use RvMedia;

class ZapImoveisController extends BaseController
{
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository;

    /**
     * ZapImoveisController constructor.
     * @param PropertyInterface $propertyRepository
     */
    public function __construct(PropertyInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * Display ZAP Imóveis integration page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        page_title()->setTitle(trans('plugins/real-estate::property.zap_imoveis_integration'));

        $xmlPath = $this->getXmlPath();
        $xmlExists = File::exists($xmlPath);
        $lastUpdated = $xmlExists ? File::lastModified($xmlPath) : null;

        if ($lastUpdated) {
            $lastUpdated = date('d/m/Y H:i:s', $lastUpdated);
        }

        return view('plugins/real-estate::zap-imoveis.index', compact('xmlExists', 'lastUpdated'));
    }

    /**
     * Generate XML file for ZAP Imóveis
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function generate(Request $request, BaseHttpResponse $response)
    {
        try {
            $xml = $this->generateXml();
            $xmlPath = $this->getXmlPath();

            File::put($xmlPath, $xml);

            return $response
                ->setMessage(trans('plugins/real-estate::property.zap_imoveis_xml_generated_successfully'))
                ->setData(['path' => route('zap-imoveis.download')]);

        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * Download the XML file
     *
     * @param BaseHttpResponse $response
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(BaseHttpResponse $response)
    {
        $xmlPath = $this->getXmlPath();

        if (File::exists($xmlPath)) {
            return response()->download($xmlPath, 'zap_imoveis.xml');
        }

        return $response
            ->setError()
            ->setMessage(trans('plugins/real-estate::property.zap_imoveis_xml_not_found'))
            ->setNextUrl(route('zap-imoveis.index'));
    }

    /**
     * Get the path to the XML file
     *
     * @return string
     */
    protected function getXmlPath()
    {
        $uploadPath = public_path('storage/zap-imoveis');

        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        return $uploadPath . '/zap_imoveis.xml';
    }

    /**
     * Generate XML for ZAP Imóveis
     *
     * @return string
     */
    protected function generateXml()
    {
        try {
            // Get all active properties with necessary relationships loaded
            $properties = $this->propertyRepository->getModel()
                ->where('moderation_status', 'approved')
                ->with(['features', 'currency', 'categories', 'city', 'city.state'])
                ->get();

            // Create XML structure
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ListingDataFeed xmlns="http://www.vivareal.com/schemas/1.0/VRSync" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.vivareal.com/schemas/1.0/VRSync http://xml.vivareal.com/vrsync.xsd"></ListingDataFeed>');

            // Add Header
            $header = $xml->addChild('Header');
            $header->addChild('Provider', htmlspecialchars(setting('site_title', config('app.name'))));
            $header->addChild('Email', setting('admin_email', 'contato@seusite.com.br')); // Corrected Email: Plain string - Removed array format
            $header->addChild('ContactName', htmlspecialchars(setting('admin_name', 'Administrador')));
            $header->addChild('PublishDate', (new DateTime())->format('Y-m-d\TH:i:s'));
            $header->addChild('Telephone', htmlspecialchars(setting('admin_phone', '(11) 0000-0000')));

            // Add Listings
            $listings = $xml->addChild('Listings');

            foreach ($properties as $property) {
                $this->addPropertyToXml($listings, $property);
            }

            // Convert to string with proper formatting
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());

            return $dom->saveXML();
        } catch (Exception $e) {
            \Log::error('Erro ao gerar XML: ' . $e->getMessage());
            throw new Exception('Erro ao gerar XML: ' . $e->getMessage());
        }
    }

    /**
     * Add a property to the XML
     *
     * @param \SimpleXMLElement $listings
     * @param Property $property
     * @return void
     */
    protected function addPropertyToXml(\SimpleXMLElement $listings, Property $property)
    {
        try {
            $listing = $listings->addChild('Listing');

            // Basic Information
            $listing->addChild('ListingID', $property->id);

            // Title - Using CDATA
            $title = $listing->addChild('Title');
            $titleDom = dom_import_simplexml($title);
            $titleDom->appendChild($titleDom->ownerDocument->createCDATASection($property->name));

            // Transaction Type
            $transactionType = 'For Sale';
            if ($property->type) {
                $transactionType = $property->type->getValue() === PropertyTypeEnum::SALE
                    ? 'For Sale'
                    : ($property->type->getValue() === PropertyTypeEnum::RENT ? 'For Rent' : 'Sale/Rent');
            }
            $listing->addChild('TransactionType', $transactionType);

            $listing->addChild('PublicationType', 'STANDARD');

            // URL
            if ($property->slug) {
                $url = route('public.properties') . '/' . $property->slug;
                $listing->addChild('DetailViewUrl', $url);
            }

            // Media
            if (is_array($property->images) && count($property->images) > 0) {
                $media = $listing->addChild('Media');
                $isPrimary = true;
                foreach ($property->images as $image) {
                    if (!empty($image)) {
                        $imageUrl = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
                        $item = $media->addChild('Item', htmlspecialchars($imageUrl));
                        $item->addAttribute('medium', 'image');
                        $item->addAttribute('caption', htmlspecialchars(substr($property->name, 0, 50)));
                        if ($isPrimary) {
                            $item->addAttribute('primary', 'true');
                            $isPrimary = false;
                        }
                    }
                }
            }

            // Details
            $details = $listing->addChild('Details');

            // Property Type - Enhanced Mapping (more specific types)
            $usageType = 'Residential';
            $propertyType = 'Residential / Home';

            if ($property->category) {
                $categoryName = strtolower($property->category->name);

                if (strpos($categoryName, 'comercial') !== false) {
                    $usageType = 'Commercial';
                    if (strpos($categoryName, 'sala') !== false || strpos($categoryName, 'conjunto') !== false || strpos($categoryName, 'escritório') !== false) {
                        $propertyType = 'Commercial / Office';
                    } elseif (strpos($categoryName, 'loja') !== false || strpos($categoryName, 'ponto') !== false) {
                        $propertyType = 'Commercial / Business';
                    } elseif (strpos($categoryName, 'galpão') !== false || strpos($categoryName, 'depósito') !== false || strpos($categoryName, 'armazém') !== false) {
                        $propertyType = 'Commercial / Industrial';
                    } elseif (strpos($categoryName, 'prédio') !== false || strpos($categoryName, 'edifício residencial') !== false) {
                        $propertyType = 'Commercial / Edificio Residencial';
                    } elseif (strpos($categoryName, 'consultório') !== false) {
                        $propertyType = 'Commercial / Consultorio';
                    }  elseif (strpos($categoryName, 'edifício') !== false) {
                        $propertyType = 'Commercial / Edificio Comercial';
                    } else {
                        $propertyType = 'Commercial / Building';
                    }
                } elseif (strpos($categoryName, 'apartamento') !== false) {
                    $propertyType = 'Residential / Apartment';
                } elseif (strpos($categoryName, 'casa de condomínio') !== false) {
                    $propertyType = 'Residential / Condo';
                } elseif (strpos($categoryName, 'casa de vila') !== false) {
                    $propertyType = 'Residential / Village House';
                } elseif (strpos($categoryName, 'chácara') !== false || strpos($categoryName, 'fazenda') !== false || strpos($categoryName, 'sítio') !== false) {
                    $propertyType = 'Residential / Farm Ranch';
                } elseif (strpos($categoryName, 'cobertura') !== false) {
                    $propertyType = 'Residential / Penthouse';
                } elseif (strpos($categoryName, 'flat') !== false) {
                    $propertyType = 'Residential / Flat';
                } elseif (strpos($categoryName, 'kitnet') !== false || strpos($categoryName, 'conjugado') !== false) {
                    $propertyType = 'Residential / Kitnet';
                } elseif (strpos($categoryName, 'studio') !== false || strpos($categoryName, 'estúdio') !== false) {
                    $propertyType = 'Residential / Studio';
                } elseif (strpos($categoryName, 'terreno') !== false || strpos($categoryName, 'lote') !== false) {
                    $propertyType = 'Residential / Land Lot';
                    $usageType = 'Residential'; // Or Commercial if commercial land
                } elseif (strpos($categoryName, 'sobrado') !== false) {
                    $propertyType = 'Residential / Sobrado';
                } else {
                    $propertyType = 'Residential / Home';
                }
            }

            $details->addChild('UsageType', $usageType);
            $details->addChild('PropertyType', $propertyType);

            // Description - Using CDATA (and stripping HTML tags)
            $plainDescription = strip_tags($property->content ?: $property->description ?: 'Sem descrição disponível'); // Stripping HTML
            $description = $details->addChild('Description');
            $descriptionDom = dom_import_simplexml($description);
            $descriptionDom->appendChild($descriptionDom->ownerDocument->createCDATASection($plainDescription));

            // Prices
            if (!$property->type || $property->type->getValue() === PropertyTypeEnum::SALE) {
                $listPrice = $details->addChild('ListPrice', max(1, (int)$property->price));
                $listPrice->addAttribute('currency', 'BRL');
            } else {
                $rentalPrice = $details->addChild('RentalPrice', max(1, (int)$property->price));
                $rentalPrice->addAttribute('currency', 'BRL');
                $rentalPrice->addAttribute('period', 'Monthly');
            }

            // Area - LotArea for Land Lot, LivingArea for others
            $area = max(1, (int)$property->square);
            if ($propertyType === 'Residential / Land Lot' || $propertyType === 'Commercial / Land Lot') {
                $lotArea = $details->addChild('LotArea', $area);
                $lotArea->addAttribute('unit', 'square metres');
                // Do NOT add LivingArea for Land Lots
            } else {
                $livingArea = $details->addChild('LivingArea', $area);
                $livingArea->addAttribute('unit', 'square metres');
            }


            // Bedrooms and Bathrooms - Conditionally adding (only for relevant types)
            if (!in_array($propertyType, ['Residential / Land Lot', 'Residential / Farm Ranch', 'Commercial / Industrial', 'Commercial / Building', 'Commercial / Land Lot', 'Commercial / Business'])) {
                $details->addChild('Bedrooms', $property->number_bedroom ?: 0);
                $details->addChild('Bathrooms', $property->number_bathroom ?: 0);
            }


            // Optional Details (Condomínio, IPTU, Suites, Garage, YearBuilt)
            if ($property->property_administration_fee) {
                $propertyAdminFee = $details->addChild('PropertyAdministrationFee', max(0, (int)$property->property_administration_fee));
                $propertyAdminFee->addAttribute('currency', 'BRL');
            }
            if ($property->iptu) {
                $iptu = $details->addChild('Iptu', max(0, (int)$property->iptu));
                $iptu->addAttribute('currency', 'BRL');
                $iptu->addAttribute('period', 'Yearly');
            }
            if ($property->number_suite) {
                $details->addChild('Suites', max(0, (int)$property->number_suite));
            }
            if ($property->number_garage) {
                $details->addChild('Garage', max(0, (int)$property->number_garage));
            }
            if ($property->year_built) {
                $details->addChild('YearBuilt', max(1900, min(date('Y'), (int)$property->year_built)));
            }


            // Features - Example (Adapt featureMap to YOUR system's features!)
            $featureMap = [ // **IMPORTANT: Adapt this mapping to YOUR system's features!**
                'Piscina' => 'Pool',
                'Churrasqueira' => 'BBQ',
                'Varanda' => 'Balcony',
                'Portão Eletrônico' => 'Electronic Gate',
                // ... add your mappings here based on the documentation ...
            ];
            if ($property->features && count($property->features) > 0) {
                $features = $details->addChild('Features');
                foreach ($property->features as $feature) {
                    if ($feature && $feature->name && isset($featureMap[$feature->name])) { // Check if mapping exists
                        $features->addChild('Feature', htmlspecialchars($featureMap[$feature->name]));
                    }
                }
            } else {
                $details->addChild('Features'); // Add empty Features tag if no features mapped - as per documentation, Features tag should always be present
            }


            // Location
            $location = $listing->addChild('Location');
            $location->addChild('Country', 'Brasil')->addAttribute('abbreviation', 'BR');
            if ($property->city && $property->city->state) { // Check if city and state are loaded
                 $location->addChild('State', htmlspecialchars($property->city->state->name))
                    ->addAttribute('abbreviation', htmlspecialchars($property->city->state->abbreviation ?? 'MG'));
                $location->addChild('City', htmlspecialchars($property->city->name));
            } else {
                $location->addChild('State', 'Minas Gerais')->addAttribute('abbreviation', 'MG'); // Default values if missing
                $location->addChild('City', 'São Lourenço');
            }
            if ($property->location) {
                $location->addChild('Neighborhood', htmlspecialchars($property->location));
            } else {
                $location->addChild('Neighborhood', 'Centro'); // Default neighborhood if missing
            }
            $postalCode = $property->city && $property->city->postal_code ? $property->city->postal_code : '37470-000';
            $location->addChild('PostalCode', htmlspecialchars($postalCode));
            if ($property->latitude && $property->longitude) {
                $location->addChild('Latitude', $property->latitude);
                $location->addChild('Longitude', $property->longitude);
            }
            if ($property->address) {
                $location->addChild('Address', htmlspecialchars($property->address));
            }
            if ($property->street_number) {
                $location->addChild('StreetNumber', htmlspecialchars($property->street_number));
            }
            if ($property->complement) {
                $location->addChild('Complement', htmlspecialchars($property->complement));
            }
            $location->addAttribute('displayAddress', 'All');


            // Contact Info
            $contactInfo = $listing->addChild('ContactInfo');
            $contactInfo->addChild('Name', htmlspecialchars(setting('site_title', config('app.name'))));
            $contactInfo->addChild('Email', setting('admin_email', 'contato@seusite.com.br')); // Corrected Email: Plain string - Removed array format
            $contactInfo->addChild('Website', htmlspecialchars(url('/')));
            $contactInfo->addChild('Telephone', htmlspecialchars(setting('admin_phone', '(11) 0000-0000')));


        } catch (Exception $e) {
            \Log::error('Erro ao adicionar propriedade ao XML: ' . $e->getMessage() . ' - ID: ' . $property->id);
        }
    }
}