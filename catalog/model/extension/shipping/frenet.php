<?php
class ModelExtensionShippingFrenet extends Model {
    private $servicos = array();
    private $url = '';
    private $quote_data = array();

    private $cep_destino;
    private $cep_origem;
    private $pais_destino;

    private $mensagem_erro = array();

    protected function get_coupon() {
        if (!isset($this->session->data['coupon'])) {
            return null;
        }

        return $this->session->data['coupon'];
    }

    // função responsável pelo retorno à loja dos valores finais dos valores dos fretes
    /**
     * @param $address
     * @return array
     */
    public function getQuote($address) {
        $this->load->language('extension/shipping/frenet');

        $method_data = array();

        $produtos = $this->cart->getProducts();

        // obtém só a parte numérica do CEP
        $this->cep_origem = preg_replace ("/[^0-9]/", '', $this->config->get('shipping_frenet_postcode'));
        $this->cep_destino = preg_replace ("/[^0-9]/", '', $address['postcode']);

        $this->pais_destino='BR';
        $this->load->model('localisation/country');
        $country_info = $this->model_localisation_country->getCountry($address['country_id']);
        if ($country_info) {
            $this->pais_destino = $country_info['iso_code_2'];
        }

        // product array
        $shippingItemArray = array();
        $count = 0;

        foreach ($produtos as $prod) {
            $qty = $prod['quantity'];
            $shippingItem = new stdClass();

            $shippingItem->Weight = $this->getPesoEmKg($prod['weight_class_id'], $prod['weight']) / $qty;
            $shippingItem->Length = $this->getDimensaoEmCm($prod['length_class_id'], $prod['length']);
            $shippingItem->Height = $this->getDimensaoEmCm($prod['length_class_id'], $prod['height']);
            $shippingItem->Width = $this->getDimensaoEmCm($prod['length_class_id'], $prod['width']);
            $shippingItem->Diameter = 0;
            $shippingItem->SKU = '';
            $shippingItem->Category = '';
            $shippingItem->isFragile=false;

            //$this->log->write( 'shippingItem: ' . print_r($shippingItem, true));

            $shippingItem->Quantity = $qty;

            $shippingItemArray[$count] = $shippingItem;
            $count++;
        }

        $coupon = $this->get_coupon();

        $service_param = array (
            'quoteRequest' => array(
                'Username' => $this->config->get('shipping_frenet_contrato_codigo'),
                'Password' => $this->config->get('shipping_frenet_contrato_senha'),
                'Token' => $this->config->get('shipping_frenet_contrato_token'),
                'Coupom' => $coupon,
                'PlatformName' => 'Magento',
                'PlatformVersion' => VERSION,
                'SellerCEP' => $this->cep_origem,
                'RecipientCEP' => $this->cep_destino,
                'RecipientDocument' => '',
                'ShipmentInvoiceValue' => $this->cart->getSubTotal(),
                'ShippingItemArray' => $shippingItemArray,
                'RecipientCountry' => $this->pais_destino
            )
        );

        //invocarFrenet($service_param);

        //$this->log->write('service_param: ' . print_r($service_param, true));

        $this->setUrl();

        // Gets the WebServices response.
        ini_set('soap.wsdl_cache_enabled', '0');
        $client = new SoapClient($this->url, array("soap_version" => SOAP_1_1,"trace" => 1));
        $response = $client->__soapCall("GetShippingQuote", array($service_param));

        //$this->log->write(  $client->__getLastRequest());
        //$this->log->write(  $client->__getLastResponse());

        $values = array();

        if ( isset( $response->GetShippingQuoteResult ) && isset($response->GetShippingQuoteResult->ShippingSevicesArray)
            && isset($response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices)) {
            if(count($response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices)==1)
                $servicosArray[0] = $response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices;
            else
                $servicosArray = $response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices;

            foreach($servicosArray as $servicos){
                if (!isset($servicos->ServiceCode) || $servicos->ServiceCode . '' == '' || !isset($servicos->ShippingPrice)) {
                    continue;
                }

                if (!isset($servicos->ShippingPrice))
                    continue;

                if (isset($servicos->DeliveryTime))
                    $deliveryTime=$servicos->DeliveryTime;

                if ( $deliveryTime > 0 && $this->config->get('shipping_frenet_msg_prazo') ) {
                    $label = sprintf($this->config->get('shipping_frenet_msg_prazo'), $servicos->ServiceDescription, (int)$deliveryTime);
                } else {
                    $label = $servicos->ServiceDescription;
                }

                $cost  = floatval(str_replace(",", ".", (string) $servicos->ShippingPrice));
                if (version_compare(VERSION, '2.2') < 0) {
                    $text = $this->currency->format($this->tax->calculate($cost, $this->config->get('frenet_tax_class_id'), $this->config->get('config_tax')));
                } else {
                    $text = $this->currency->format($this->tax->calculate($cost, $this->config->get('frenet_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                }

                $this->quote_data[$servicos->ServiceCode] = array(
                    'code'         => 'frenet.' . $servicos->ServiceCode,
                    'title'        => $label,
                    'cost'         => $cost,
                    'tax_class_id' => $this->config->get('frenet_tax_class_id'),
                    'text'         => $text
                );
            }
        }

        // ajustes finais
        if ($this->quote_data) {
            $method_data = array(
                'code'       => 'frenet',
                'title'      => $this->language->get('text_title'),
                'quote'      => $this->quote_data,
                'sort_order' => $this->config->get('shipping_frenet_sort_order'),
                'error'      => false
            );
        } else if (!empty($this->mensagem_erro)) {
            $method_data = array(
                'code'       => 'frenet',
                'title'      => $this->language->get('text_title'),
                'quote'      => $this->quote_data,
                'sort_order' => $this->config->get('shipping_frenet_sort_order'),
                'error'      => implode('<br />', $this->mensagem_erro)
            );
        }

        return $method_data;
    }

    // prepara a url de chamada ao site dos frenet
    private function setUrl(){
        $url = "http://services.frenet.com.br/logistics/ShippingQuoteWS.asmx?wsdl";

        $this->url = $url;
    }

    // prepara a url de chamada ao site dos frenet
    private function setApiUrl(){
        $url = "http://api.frenet.com.br/shipping/quote";

        $this->url = $url;
    }

    // retorna a dimensão em centímetros
    private function getDimensaoEmCm($unidade_id, $dimensao){
        if (is_numeric($dimensao)) {
            $length_class_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class mc LEFT JOIN " . DB_PREFIX . "length_class_description mcd ON (mc.length_class_id = mcd.length_class_id) WHERE mcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mc.length_class_id =  '" . (int)$unidade_id . "'");

            if (isset($length_class_product_query->row['unit'])) {
                if ($length_class_product_query->row['unit'] == 'mm') {
                    return $dimensao / 10;
                }
            }
        }
        return $dimensao;
    }

    // retorna o peso em quilogramas
    private function getPesoEmKg($unidade_id, $peso){
        if (is_numeric($peso)) {
            $weight_class_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wc.weight_class_id =  '" . (int)$unidade_id . "'");

            if (isset($weight_class_product_query->row['unit'])) {
                if ($weight_class_product_query->row['unit'] == 'g') {
                    return ($peso / 1000);
                }
            }
        }
        return $peso;
    }
}
?>
