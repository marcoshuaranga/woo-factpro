=== Factpro for WooCommerce ===
Contributors: factpro
Tags: Woocommerce
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 3.3.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Electronic invoicing for Peru with Factpro.pe integration

== Description ==

This plugin integrates WooCommerce with Factpro.pe to enable electronic invoicing for Peruvian businesses. It provides seamless generation, management, and retrieval of electronic invoices directly from your WooCommerce store.

== External Services ==

This plugin connects to external services to generate electronic invoices and to optionally look up DNI/RUC data. These services are only used when you configure them and trigger the related actions.

* Factpro API (invoicing): https://api.factpro.la and https://dev.factpro.la
	* Data sent: order and customer data required to generate the electronic invoice (such as document type/number, customer name, address, items, totals, and taxes).
* DNI/RUC lookup providers (optional, selected in settings):
	* Factpro API: https://docs.factpro.la/api-consulta-ruc-y-dni
	* Migo API: https://api.migo.pe
	* PeruDev API: https://apiperu.dev
	* Data sent: document number (DNI/RUC) and the API token you configure for the chosen provider.

By enabling these features and configuring the tokens, you consent to the transmission of the above data to the selected provider. Please review each provider’s terms and privacy policy before use.

== Features ==

* Automatic electronic invoice generation
* Invoice status tracking
* PDF and XML document downloads
* Support for invoices and receipts