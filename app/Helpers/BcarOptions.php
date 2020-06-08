<?php
namespace App\Helpers;

class BcarOptions
{
    /**
     * Return an array of all BCAR options needed for query
     *
     * @return array $bcarOptions An array of options for each class
     */
    public static function all($offset = 1)
    {
        $waterfront = 'LIST_192';

        $bcarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AL02DCSMHGA',
                'Sold'   => '1AL02DCSMKOK',
                'Contingent' => 'PWC_1AL02DCSI53S',
                'Pending' => '1AL02DCSMNK7',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150204172056869833000000,GF20150204172056907082000000,GF20150204172057113687000000,GF20150204172056829043000000,GF20150204172057197731000000,GF20150204172056617468000000,GF20150204172056790876000000,GF20150204172056580165000000,GF20150204172056948623000000,GF20150204172057026304000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82,LIST_26,LIST_95',
            ],
            // 'B' => [
            //     'Offset' => $offset,
            //     'Limit'  => 10000,
            //     'Active' => '1AL02DCUQYVE',
            //     'Sold'   => '1AL02DCUREWY',
            //     'Contingent' => 'PWC_1AM4FUKPK7MT',
            //     'Pending' => '1AL02DCUR1U2',
            //     'Select' =>
            //         'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            // ],
            'C' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AL02DCZGMC3',
                'Sold'   => '1AL02DCZH5AM',
                'Contingent' => 'PWC_1AM4FUKPKDRX',
                'Pending' => '1AL02DCZGOXQ',
                'Select' => 'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150204172059157726000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82,LIST_26,LIST_95',
            ],
            'E' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AMY7USONV0G',
                'Sold'   => '1AMY7USONXV4',
                'Contingent' => 'PWC_1AMY7URID3UE',
                'Pending' => '1AMY7USOO02O',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150506150258346595000000,GF20150506150244143322000000,GF20150506150120689916000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_78,LIST_80,LIST_82,LIST_26,LIST_95',
            ],
            'F' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AMY85IONHFJ',
                'Sold'   => '1AMY85IONKFQ',
                'Contingent' => 'PWC_1AMY85I5VEVF',
                'Pending' => '1AMY85IONNES',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150430163750842533000000,GF20150430163608793806000000,GF20150430163732973981000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_53,LIST_56,LIST_199,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AL02DE2AFC9',
                'Sold'   => '1AM3QB6V6U7E',
                'Contingent' => 'PWC_1AM4FUKPKNL5',
                'Pending' => '1AMY85IONNES',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150204172058417689000000,GF20150428144746131692000000,GF20150204172058725166000000,GF20150204172058691139000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'J' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Active' => '1AL02DECQ0EX',
                'Sold'   => '1AM3QB6V6XWY',
                'Contingent' => 'PWC_1AM4FUKPKX59',
                'Pending' => '1AL02DECQ5OS',
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150428162423317708000000,GF20150428170540841152000000,GF20150204172057327961000000,LIST_88,LIST_89,LIST_90,LIST_56,LIST_78,LIST_80,LIST_82,LIST_26,LIST_95',
            ],
        ];

        return $bcarOptions;
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
        $waterfront = 'LIST_192';

        $bcarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 1000,
                'Select' =>
                    'LIST_3',
            ],
            // 'B' => [
            //     'Select' =>
            //         'LIST_3',
            // ],
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
            // 'I' => [
            //     'Select' =>
            //         'LIST_3',
            // ],
        ];

        return $bcarOptions;
    }

    /**
     * Return an array of all BCAR options needed for query
     *
     * @return array $bcarOptions An array of options for each class
     */
    public static function singleListing($offset = 0)
    {
        $waterfront = 'LIST_192';

        $bcarOptions = [
            'A' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150204172056869833000000,GF20150204172056907082000000,GF20150204172057113687000000,GF20150204172056829043000000,GF20150204172057197731000000,GF20150204172056617468000000,GF20150204172056790876000000,GF20150204172056580165000000,GF20150204172056948623000000,GF20150204172057026304000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            // 'B' => [
            //     'Offset' => $offset,
            //     'Limit'  => 1,
            //     'Select' =>
            //         'LIST_3,LIST_5,LIST_6,LIST_8,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_64,LIST_68,LIST_69,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            // ],
            'C' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' => 'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150204172059157726000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_56,LIST_75,LIST_76,LIST_78,LIST_80,LIST_82',
            ],
            'E' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150506150258346595000000,GF20150506150244143322000000,GF20150506150120689916000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_78,LIST_80,LIST_82',
            ],
            'F' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_77,LIST_87,LIST_94,' . $waterfront . ',GF20150430163750842533000000,GF20150430163608793806000000,GF20150430163732973981000000,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,LIST_88,LIST_89,LIST_90,LIST_53,LIST_56,LIST_199,LIST_78,LIST_80,LIST_82',
            ],
            'G' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_37,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_48,LIST_57,LIST_66,LIST_67,LIST_77,LIST_87,LIST_94,LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150204172058417689000000,GF20150428144746131692000000,GF20150204172058725166000000,GF20150204172058691139000000,LIST_88,LIST_89,LIST_90,LIST_146,LIST_53,LIST_56,LIST_199,LIST_68,LIST_69,LIST_78,LIST_80,LIST_82',
            ],
            'J' => [
                'Offset' => $offset,
                'Limit'  => 1,
                'Select' =>
                    'LIST_3,LIST_5,LIST_6,LIST_8,LIST_9,LIST_10,LIST_12,LIST_15,LIST_22,LIST_23,LIST_29,LIST_31,LIST_34,LIST_35,LIST_39,LIST_40,LIST_43,LIST_46,LIST_47,LIST_57,LIST_77,LIST_87,LIST_94,' . $waterfront . ',LIST_133,LIST_106,LIST_165,LIST_166,listing_member_shortid,colisting_member_shortid,GF20150428162423317708000000,GF20150428170540841152000000,GF20150204172057327961000000,LIST_88,LIST_89,LIST_90,LIST_56,LIST_78,LIST_80,LIST_82',
            ],
        ];

        return $bcarOptions;
    }
}
