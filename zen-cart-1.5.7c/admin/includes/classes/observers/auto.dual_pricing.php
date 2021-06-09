<?php

class zcObserverDualPricing extends base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_ADMIN_CUSTOMERS_CUSTOMER_UPDATE',    // insert customer data to table customers
            'NOTIFY_ADMIN_CUSTOMERS_CUSTOMER_EDIT',      // add form field to customer edit
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_HEADER',     // add column headers
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_NEW_FIELDS', // add search fields
            'NOTIFY_ADMIN_CUSTOMERS_LISTING_ELEMENT',    // add column content
        ]);
    }

    /**
     * @param $class
     * @param $eventID
     * @param $p1
     * @param $p2
     */
    protected function notify_admin_customers_customer_update(&$class, $eventID, $p1, &$p2): void
    {
        $customers_whole = !empty($_POST['customers_whole']) ? zen_db_prepare_input($_POST['customers_whole']) : '0';
        $p2[] = ['fieldName' => 'customers_whole', 'value' => $customers_whole, 'type' => 'stringIgnoreNull'];
    }

    /**
     * @param $class
     * @param $eventID
     * @param $p1
     * @param $p2
     * @param $p3
     */
    protected function notify_admin_customers_customer_edit(&$class, $eventID, $p1, &$p2, &$p3): void
    {
        $input_wholesale = zen_draw_input_field('customers_whole', htmlspecialchars($p1->customers_whole, ENT_COMPAT, CHARSET, true),
            zen_set_field_length(TABLE_CUSTOMERS, 'customers_whole', 50) . ' class="form-control" id="customers_whole', true);
        $p2[] = ['label' => ENTRY_WHOLESALE_PRICING_LEVEL, 'for' => 'customers_whole', 'input' => $input_wholesale];
    }

    /**
     * @param $class
     * @param $eventID
     * @param $p1
     * @param $p2
     */
    protected function notify_admin_customers_listing_header(&$class, $eventID, $p1, &$p2): void
    {
        //these constants added in ZC158
        if (!defined('TEXT_ASC')) {
            define('TEXT_ASC', 'Asc');
        }
        if (!defined('TEXT_DESC')) {
            define('TEXT_DESC', 'Desc');
        }

        $wholesale_header = (($_GET['list_order'] === 'wholesale-asc' || $_GET['list_order'] === 'wholesale-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_WHOLESALE . '</span>'
                : TABLE_HEADING_WHOLESALE) . '<br>' .
            '<a href="' . zen_href_link(FILENAME_CUSTOMERS, zen_get_all_get_params(['list_order', 'page']) . 'list_order=wholesale-asc', 'NONSSL') . '">' . ($_GET['list_order'] === 'wholesale-asc'
                ? '<span class="SortOrderHeader">' . TEXT_ASC . '</span>' : '<span class="SortOrderHeaderLink">' . TEXT_ASC . '</span>') . '</a>&nbsp;' .
            '<a href="' . zen_href_link(FILENAME_CUSTOMERS, zen_get_all_get_params(['list_order', 'page']) . 'list_order=wholesale-desc', 'NONSSL') . '">' . ($_GET['list_order'] === 'wholesale-desc'
                ? '<span class="SortOrderHeader">' . TEXT_DESC . '</span>' : '<span class="SortOrderHeaderLink">' . TEXT_DESC . '</span>') . '</a>';
        $p2[] = ['content' => $wholesale_header];
    }

    /**
     * @param $class
     * @param $eventID
     * @param $p1
     * @param $p2
     * @param $p3
     */
    protected function notify_admin_customers_listing_new_fields(&$class, $eventID, $p1, &$p2, &$p3): void
    {
        //add column to query
        $p2 = ', c.customers_whole';

        //allow sorting by header, on search results
        if (isset($_GET['list_order'])) {
            $disp_order = '';
            switch ($_GET['list_order']) {
                case 'wholesale':
                    $disp_order = "c.customers_whole";
                    break;
                case 'wholesale-desc':
                    $disp_order = "c.customers_whole DESC";
                    break;
            }
            if ($disp_order !== '') {
                $p3 = $disp_order;
            }
        }
    }

    /**
     * @param $class
     * @param $eventID
     * @param $p1
     * @param $p2
     */
    protected function notify_admin_customers_listing_element(&$class, $eventID, $p1, &$p2): void
    {
        $p2[] = ['content' => $p1['customers_whole']];
    }
}
