<?php
namespace App\Helpers;

class EcarOptions
{
    /**
     * Return an array of all ECAR options needed for query
     *
     * @param int $offset The offset for a paginated query
     *
     * @return array $ecarOptions An array of options for each class
     */
    public static function all($offset = 1)
    {
        $waterfront = 'GF20131203222329624962000000'; //mother of god

        $ecarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 5000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_58,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131203203513863218000000,GF20131203203523234694000000,GF20131203203501805928000000,GF20131203185526796706000000,GF20131203203446527084000000,GF20131203185458688530000000,GF20131203222306734642000000,GF20131203222538613490000000,LIST_88,LIST_89,LIST_90,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82,LIST_147',
            ],
            'B' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131230164912692795000000,GF20131230164915907956000000,GF20131230164916093183000000,GF20131230164916157466000000,GF20131230164914843719000000,GF20131230164914069211000000,GF20131230164913550188000000,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'C' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131231131427333528000000,GF20131231131427101593000000,GF20131231131427184540000000,GF20131231201806058732000000,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'E' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140103161837200256000000,LIST_56,LIST_74,LIST_78,LIST_80,LIST_82',
            ],
            'F' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140106175333111396000000,LIST_74,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Offset' => $offset,
                'Limit'  => 9000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131230211343236208000000,GF20131230211344214865000000,GF20131230211345452659000000,GF20131230211345387488000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'H' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140122222400891202000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',

            ],
            'I' => [
                'Offset' => $offset,
                'Limit'  => 10000,
                'Active' => '',
                'Sold'   => '',
                'Contingent' => '',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_146,LIST_53,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
        ];

        return $ecarOptions;
    }

    /**
     * Return an array of all ECAR options needed for query
     *
     * @param int $offset The offset for a paginated query
     *
     * @return array $ecarOptions An array of options for each class
     */
    public static function idList($offset = 1)
    {
        $waterfront = 'GF20131203222329624962000000'; //mother of god

        $ecarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Select' =>
                    'LIST_3',
            ],
            'B' => [
                'Select' =>
                    'LIST_3',
            ],
            'C' => [
                'Select' =>
                    'LIST_3',
            ],
            'E' => [
                'Select' =>
                    'LIST_3',
            ],
            'F' => [
                'Select' =>
                    'LIST_3',
            ],
            'G' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Select' =>
                    'LIST_3',
            ],
            'H' => [
                'Select' =>
                    'LIST_3',

            ],
            'I' => [
                'Select' =>
                    'LIST_3',
            ],
        ];

        return $ecarOptions;
    }

    /**
     * Return an array of all ECAR options needed for query
     *
     * @param int $offset The offset for a paginated query
     *
     * @return array $ecarOptions An array of options for each class
     */
    public static function singleListing($offset = 0)
    {
        $waterfront = 'GF20131203222329624962000000'; //mother of god

        $ecarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_58,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131203203513863218000000,GF20131203203523234694000000,GF20131203203501805928000000,GF20131203185526796706000000,GF20131203203446527084000000,GF20131203185458688530000000,GF20131203222306734642000000,GF20131203222538613490000000,LIST_88,LIST_89,LIST_90,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82,LIST_147',
            ],
            'B' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131230164912692795000000,GF20131230164915907956000000,GF20131230164916093183000000,GF20131230164916157466000000,GF20131230164914843719000000,GF20131230164914069211000000,GF20131230164913550188000000,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'C' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131231131427333528000000,GF20131231131427101593000000,GF20131231131427184540000000,GF20131231201806058732000000,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'E' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140103161837200256000000,LIST_56,LIST_74,LIST_78,LIST_80,LIST_82',
            ],
            'F' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140106175333111396000000,LIST_74,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20131230211343236208000000,GF20131230211344214865000000,GF20131230211345452659000000,GF20131230211345387488000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'H' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,GF20140122222400891202000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',

            ],
            'I' => [
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,listing_member_shortid,colisting_member_shortid,LIST_146,LIST_53,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
        ];

        return $ecarOptions;
    }
}
