<?php

namespace Srapid\RealEstate\Http\Controllers;

use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Repositories\Interfaces\PropertyInterface;
use Srapid\RealEstate\Enums\PropertyTypeEnum;
use Srapid\RealEstate\Enums\PropertyPeriodEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RvMedia;
use DateTime;
use Exception;

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
            // Get all active properties
            $properties = $this->propertyRepository->getModel()
                ->where('moderation_status', 'approved')
                ->with(['features', 'currency', 'categories', 'city'])
                ->get();

            // Create XML structure
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ListingDataFeed xmlns="http://www.vivareal.com/schemas/1.0/VRSync" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.vivareal.com/schemas/1.0/VRSync http://xml.vivareal.com/vrsync.xsd"></ListingDataFeed>');

            // Add Header
            $header = $xml->addChild('Header');
            $header->addChild('Provider', htmlspecialchars(setting('site_title', config('app.name'))));
            $header->addChild('Email', htmlspecialchars(setting('admin_email', 'contato@seusite.com.br')));
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
            
            // Title - lidando com CDATA de forma segura
            $title = $listing->addChild('Title', htmlspecialchars($property->name));
            
            // Transaction Type
            $transactionType = 'For Sale'; // Valor padrão
            if ($property->type) {
                $transactionType = $property->type->getValue() === PropertyTypeEnum::SALE 
                    ? 'For Sale' 
                    : ($property->type->getValue() === PropertyTypeEnum::RENT ? 'For Rent' : 'Sale/Rent');
            }
            $listing->addChild('TransactionType', $transactionType);
            
            $listing->addChild('PublicationType', 'STANDARD');
            
            // URL - certifique-se de que o slug existe
            if ($property->slug) {
                $url = route('public.properties') . '/' . $property->slug;
                $listing->addChild('DetailViewUrl', $url);
            }
            
            // Media (Images)
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
            
            // Property Type Mapping
            $usageType = 'Residential';
            $propertyType = 'Residential / Apartment';
            
            if ($property->category) {
                // Map category to ZAP property types
                $categoryName = strtolower($property->category->name);
                
                if (strpos($categoryName, 'comercial') !== false) {
                    $usageType = 'Commercial';
                    $propertyType = 'Commercial / Office';
                } elseif (strpos($categoryName, 'casa') !== false || strpos($categoryName, 'sobrado') !== false) {
                    $propertyType = 'Residential / Home';
                } elseif (strpos($categoryName, 'terreno') !== false || strpos($categoryName, 'lote') !== false) {
                    $propertyType = 'Residential / Land';
                }
            }
            
            $details->addChild('UsageType', $usageType);
            $details->addChild('PropertyType', $propertyType);
            
            // Description - lidando com CDATA de forma segura
            $description = $details->addChild('Description', htmlspecialchars($property->content ?: $property->description ?: 'Sem descrição disponível'));
            
            // Prices
            if (!$property->type || $property->type->getValue() === PropertyTypeEnum::SALE) {
                $price = max(1, (int)$property->price); // Garante que o preço não seja zero
                $listPrice = $details->addChild('ListPrice', $price);
                $listPrice->addAttribute('currency', 'BRL');
            } else {
                $price = max(1, (int)$property->price); // Garante que o preço não seja zero
                $rentalPrice = $details->addChild('RentalPrice', $price);
                $rentalPrice->addAttribute('currency', 'BRL');
                $rentalPrice->addAttribute('period', 'Monthly');
            }
            
            // Property Details
            if ($property->square) {
                $area = max(1, (int)$property->square); // Garante área mínima
                $livingArea = $details->addChild('LivingArea', $area);
                $livingArea->addAttribute('unit', 'square metres');
            } else {
                // Área é obrigatória no ZAP
                $livingArea = $details->addChild('LivingArea', 50); // Valor padrão
                $livingArea->addAttribute('unit', 'square metres');
            }
            
            // Bedrooms e Bathrooms são obrigatórios no ZAP para alguns tipos
            $details->addChild('Bedrooms', $property->number_bedroom ?: 1);
            $details->addChild('Bathrooms', $property->number_bathroom ?: 1);
            
            // Features
            if ($property->features && count($property->features) > 0) {
                $features = $details->addChild('Features');
                
                foreach ($property->features as $feature) {
                    if ($feature && $feature->name) {
                        $features->addChild('Feature', htmlspecialchars($feature->name));
                    }
                }
            }
            
            // Location
            $location = $listing->addChild('Location');
            $location->addChild('Country', 'Brasil')->addAttribute('abbreviation', 'BR');
            
            // Estado e cidade
            if ($property->city && $property->city->state) {
                $location->addChild('State', htmlspecialchars($property->city->state->name))
                    ->addAttribute('abbreviation', htmlspecialchars($property->city->state->abbreviation ?? 'SP'));
                $location->addChild('City', htmlspecialchars($property->city->name));
            } else {
                // Valores padrão para estado e cidade (obrigatórios)
                $location->addChild('State', 'São Paulo')
                    ->addAttribute('abbreviation', 'SP');
                $location->addChild('City', 'São Paulo');
            }
            
            // Bairro (obrigatório)
            if ($property->location) {
                $location->addChild('Neighborhood', htmlspecialchars($property->location));
            } else {
                $location->addChild('Neighborhood', 'Centro');
            }
            
            // CEP (obrigatório)
            $location->addChild('PostalCode', '00000-000');
            
            // Coordenadas (opcional)
            if ($property->latitude && $property->longitude) {
                $location->addChild('Latitude', $property->latitude);
                $location->addChild('Longitude', $property->longitude);
            }
            
            // Display Address
            $location->addAttribute('displayAddress', 'Neighborhood');
            
            // Contact Info
            $contactInfo = $listing->addChild('ContactInfo');
            $contactInfo->addChild('Name', htmlspecialchars(setting('site_title', config('app.name'))));
            $contactInfo->addChild('Email', htmlspecialchars(setting('admin_email', 'contato@seusite.com.br')));
            $contactInfo->addChild('Website', htmlspecialchars(url('/')));
            $contactInfo->addChild('Telephone', htmlspecialchars(setting('admin_phone', '(11) 0000-0000')));
        } catch (Exception $e) {
            \Log::error('Erro ao adicionar propriedade ao XML: ' . $e->getMessage() . ' - ID: ' . $property->id);
        }
    }
}