<?php
/**
 * Description of Country
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */

class Merchant_Country {
  protected $name;
  private   $codes = array();

  public static $COUNTRIES = array(
  array( 'alpha2' => 'AF', 'name' => 'Afghanistan', 'alpha3' => 'AFG', 'numeric' => '004' ),
  array( 'alpha2' => 'AL', 'name' => 'Albania', 'alpha3' => 'ALB', 'numeric' => '008' ),
  array( 'alpha2' => 'DZ', 'name' => 'Algeria', 'alpha3' => 'DZA', 'numeric' => '012' ),
  array( 'alpha2' => 'AS', 'name' => 'American Samoa', 'alpha3' => 'ASM', 'numeric' => '016' ),
  array( 'alpha2' => 'AD', 'name' => 'Andorra', 'alpha3' => 'AND', 'numeric' => '020' ),
  array( 'alpha2' => 'AO', 'name' => 'Angola', 'alpha3' => 'AGO', 'numeric' => '024' ),
  array( 'alpha2' => 'AI', 'name' => 'Anguilla', 'alpha3' => 'AIA', 'numeric' => '660' ),
  array( 'alpha2' => 'AG', 'name' => 'Antigua and Barbuda', 'alpha3' => 'ATG', 'numeric' => '028' ),
  array( 'alpha2' => 'AR', 'name' => 'Argentina', 'alpha3' => 'ARG', 'numeric' => '032' ),
  array( 'alpha2' => 'AM', 'name' => 'Armenia', 'alpha3' => 'ARM', 'numeric' => '051' ),
  array( 'alpha2' => 'AW', 'name' => 'Aruba', 'alpha3' => 'ABW', 'numeric' => '533' ),
  array( 'alpha2' => 'AU', 'name' => 'Australia', 'alpha3' => 'AUS', 'numeric' => '036' ),
  array( 'alpha2' => 'AT', 'name' => 'Austria', 'alpha3' => 'AUT', 'numeric' => '040' ),
  array( 'alpha2' => 'AX', 'name' => 'Åland Islands', 'alpha3' => 'ALA', 'numeric' => '248' ),
  array( 'alpha2' => 'AZ', 'name' => 'Azerbaijan', 'alpha3' => 'AZE', 'numeric' => '031' ),
  array( 'alpha2' => 'BS', 'name' => 'Bahamas', 'alpha3' => 'BHS', 'numeric' => '044' ),
  array( 'alpha2' => 'BH', 'name' => 'Bahrain', 'alpha3' => 'BHR', 'numeric' => '048' ),
  array( 'alpha2' => 'BD', 'name' => 'Bangladesh', 'alpha3' => 'BGD', 'numeric' => '050' ),
  array( 'alpha2' => 'BB', 'name' => 'Barbados', 'alpha3' => 'BRB', 'numeric' => '052' ),
  array( 'alpha2' => 'BY', 'name' => 'Belarus', 'alpha3' => 'BLR', 'numeric' => '112' ),
  array( 'alpha2' => 'BE', 'name' => 'Belgium', 'alpha3' => 'BEL', 'numeric' => '056' ),
  array( 'alpha2' => 'BZ', 'name' => 'Belize', 'alpha3' => 'BLZ', 'numeric' => '084' ),
  array( 'alpha2' => 'BJ', 'name' => 'Benin', 'alpha3' => 'BEN', 'numeric' => '204' ),
  array( 'alpha2' => 'BM', 'name' => 'Bermuda', 'alpha3' => 'BMU', 'numeric' => '060' ),
  array( 'alpha2' => 'BT', 'name' => 'Bhutan', 'alpha3' => 'BTN', 'numeric' => '064' ),
  array( 'alpha2' => 'BO', 'name' => 'Bolivia', 'alpha3' => 'BOL', 'numeric' => '068' ),
  array( 'alpha2' => 'BA', 'name' => 'Bosnia and Herzegovina', 'alpha3' => 'BIH', 'numeric' => '070' ),
  array( 'alpha2' => 'BW', 'name' => 'Botswana', 'alpha3' => 'BWA', 'numeric' => '072' ),
  array( 'alpha2' => 'BV', 'name' => 'Bouvet Island', 'alpha3' => 'BVD', 'numeric' => '074' ),
  array( 'alpha2' => 'BR', 'name' => 'Brazil', 'alpha3' => 'BRA', 'numeric' => '076' ),
  array( 'alpha2' => 'BN', 'name' => 'Brunei Darussalam', 'alpha3' => 'BRN', 'numeric' => '096' ),
  array( 'alpha2' => 'IO', 'name' => 'British Indian Ocean Territory', 'alpha3' => 'IOT', 'numeric' => '086' ),
  array( 'alpha2' => 'BG', 'name' => 'Bulgaria', 'alpha3' => 'BGR', 'numeric' => '100' ),
  array( 'alpha2' => 'BF', 'name' => 'Burkina Faso', 'alpha3' => 'BFA', 'numeric' => '854' ),
  array( 'alpha2' => 'BI', 'name' => 'Burundi', 'alpha3' => 'BDI', 'numeric' => '108' ),
  array( 'alpha2' => 'KH', 'name' => 'Cambodia', 'alpha3' => 'KHM', 'numeric' => '116' ),
  array( 'alpha2' => 'CM', 'name' => 'Cameroon', 'alpha3' => 'CMR', 'numeric' => '120' ),
  array( 'alpha2' => 'CA', 'name' => 'Canada', 'alpha3' => 'CAN', 'numeric' => '124' ),
  array( 'alpha2' => 'CV', 'name' => 'Cape Verde', 'alpha3' => 'CPV', 'numeric' => '132' ),
  array( 'alpha2' => 'KY', 'name' => 'Cayman Islands', 'alpha3' => 'CYM', 'numeric' => '136' ),
  array( 'alpha2' => 'CF', 'name' => 'Central African Republic', 'alpha3' => 'CAF', 'numeric' => '140' ),
  array( 'alpha2' => 'TD', 'name' => 'Chad', 'alpha3' => 'TCD', 'numeric' => '148' ),
  array( 'alpha2' => 'CL', 'name' => 'Chile', 'alpha3' => 'CHL', 'numeric' => '152' ),
  array( 'alpha2' => 'CN', 'name' => 'China', 'alpha3' => 'CHN', 'numeric' => '156' ),
  array( 'alpha2' => 'CX', 'name' => 'Christmas Island', 'alpha3' => 'CXR', 'numeric' => '162' ),
  array( 'alpha2' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'alpha3' => 'CCK', 'numeric' => '166' ),
  array( 'alpha2' => 'CO', 'name' => 'Colombia', 'alpha3' => 'COL', 'numeric' => '170' ),
  array( 'alpha2' => 'KM', 'name' => 'Comoros', 'alpha3' => 'COM', 'numeric' => '174' ),
  array( 'alpha2' => 'CG', 'name' => 'Congo', 'alpha3' => 'COG', 'numeric' => '178' ),
  array( 'alpha2' => 'CD', 'name' => 'Congo, the Democratic Republic of the', 'alpha3' => 'COD', 'numeric' => '180' ),
  array( 'alpha2' => 'CK', 'name' => 'Cook Islands', 'alpha3' => 'COK', 'numeric' => '184' ),
  array( 'alpha2' => 'CR', 'name' => 'Costa Rica', 'alpha3' => 'CRI', 'numeric' => '188' ),
  array( 'alpha2' => 'CI', 'name' => 'Cote D\'Ivoire', 'alpha3' => 'CIV', 'numeric' => '384' ),
  array( 'alpha2' => 'HR', 'name' => 'Croatia', 'alpha3' => 'HRV', 'numeric' => '191' ),
  array( 'alpha2' => 'CU', 'name' => 'Cuba', 'alpha3' => 'CUB', 'numeric' => '192' ),
  array( 'alpha2' => 'CY', 'name' => 'Cyprus', 'alpha3' => 'CYP', 'numeric' => '196' ),
  array( 'alpha2' => 'CZ', 'name' => 'Czech Republic', 'alpha3' => 'CZE', 'numeric' => '203' ),
  array( 'alpha2' => 'DK', 'name' => 'Denmark', 'alpha3' => 'DNK', 'numeric' => '208' ),
  array( 'alpha2' => 'DJ', 'name' => 'Djibouti', 'alpha3' => 'DJI', 'numeric' => '262' ),
  array( 'alpha2' => 'DM', 'name' => 'Dominica', 'alpha3' => 'DMA', 'numeric' => '212' ),
  array( 'alpha2' => 'DO', 'name' => 'Dominican Republic', 'alpha3' => 'DOM', 'numeric' => '214' ),
  array( 'alpha2' => 'EC', 'name' => 'Ecuador', 'alpha3' => 'ECU', 'numeric' => '218' ),
  array( 'alpha2' => 'EG', 'name' => 'Egypt', 'alpha3' => 'EGY', 'numeric' => '818' ),
  array( 'alpha2' => 'SV', 'name' => 'El Salvador', 'alpha3' => 'SLV', 'numeric' => '222' ),
  array( 'alpha2' => 'GQ', 'name' => 'Equatorial Guinea', 'alpha3' => 'GNQ', 'numeric' => '226' ),
  array( 'alpha2' => 'ER', 'name' => 'Eritrea', 'alpha3' => 'ERI', 'numeric' => '232' ),
  array( 'alpha2' => 'EE', 'name' => 'Estonia', 'alpha3' => 'EST', 'numeric' => '233' ),
  array( 'alpha2' => 'ET', 'name' => 'Ethiopia', 'alpha3' => 'ETH', 'numeric' => '231' ),
  array( 'alpha2' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'alpha3' => 'FLK', 'numeric' => '238' ),
  array( 'alpha2' => 'FO', 'name' => 'Faroe Islands', 'alpha3' => 'FRO', 'numeric' => '234' ),
  array( 'alpha2' => 'FJ', 'name' => 'Fiji', 'alpha3' => 'FJI', 'numeric' => '242' ),
  array( 'alpha2' => 'FI', 'name' => 'Finland', 'alpha3' => 'FIN', 'numeric' => '246' ),
  array( 'alpha2' => 'FR', 'name' => 'France', 'alpha3' => 'FRA', 'numeric' => '250' ),
  array( 'alpha2' => 'GF', 'name' => 'French Guiana', 'alpha3' => 'GUF', 'numeric' => '254' ),
  array( 'alpha2' => 'PF', 'name' => 'French Polynesia', 'alpha3' => 'PYF', 'numeric' => '258' ),
  array( 'alpha2' => 'TF', 'name' => 'French Southern Territories', 'alpha3' => 'ATF', 'numeric' => '260' ),
  array( 'alpha2' => 'GA', 'name' => 'Gabon', 'alpha3' => 'GAB', 'numeric' => '266' ),
  array( 'alpha2' => 'GM', 'name' => 'Gambia', 'alpha3' => 'GMB', 'numeric' => '270' ),
  array( 'alpha2' => 'GE', 'name' => 'Georgia', 'alpha3' => 'GEO', 'numeric' => '268' ),
  array( 'alpha2' => 'DE', 'name' => 'Germany', 'alpha3' => 'DEU', 'numeric' => '276' ),
  array( 'alpha2' => 'GH', 'name' => 'Ghana', 'alpha3' => 'GHA', 'numeric' => '288' ),
  array( 'alpha2' => 'GI', 'name' => 'Gibraltar', 'alpha3' => 'GIB', 'numeric' => '292' ),
  array( 'alpha2' => 'GR', 'name' => 'Greece', 'alpha3' => 'GRC', 'numeric' => '300' ),
  array( 'alpha2' => 'GL', 'name' => 'Greenland', 'alpha3' => 'GRL', 'numeric' => '304' ),
  array( 'alpha2' => 'GD', 'name' => 'Grenada', 'alpha3' => 'GRD', 'numeric' => '308' ),
  array( 'alpha2' => 'GP', 'name' => 'Guadeloupe', 'alpha3' => 'GLP', 'numeric' => '312' ),
  array( 'alpha2' => 'GU', 'name' => 'Guam', 'alpha3' => 'GUM', 'numeric' => '316' ),
  array( 'alpha2' => 'GT', 'name' => 'Guatemala', 'alpha3' => 'GTM', 'numeric' => '320' ),
  array( 'alpha2' => 'GN', 'name' => 'Guinea', 'alpha3' => 'GIN', 'numeric' => '324' ),
  array( 'alpha2' => 'GW', 'name' => 'Guinea-Bissau', 'alpha3' => 'GNB', 'numeric' => '624' ),
  array( 'alpha2' => 'GY', 'name' => 'Guyana', 'alpha3' => 'GUY', 'numeric' => '328' ),
  array( 'alpha2' => 'GG', 'name' => 'Guernsey', 'alpha3' => 'GGY', 'numeric' => '831' ),
  array( 'alpha2' => 'HT', 'name' => 'Haiti', 'alpha3' => 'HTI', 'numeric' => '332' ),
  array( 'alpha2' => 'VA', 'name' => 'Holy See (Vatican City State)', 'alpha3' => 'VAT', 'numeric' => '336' ),
  array( 'alpha2' => 'HN', 'name' => 'Honduras', 'alpha3' => 'HND', 'numeric' => '340' ),
  array( 'alpha2' => 'HK', 'name' => 'Hong Kong', 'alpha3' => 'HKG', 'numeric' => '344' ),
  array( 'alpha2' => 'HM', 'name' => 'Heard Island And Mcdonald Islands', 'alpha3' => 'HMD', 'numeric' => '334' ),
  array( 'alpha2' => 'HU', 'name' => 'Hungary', 'alpha3' => 'HUN', 'numeric' => '348' ),
  array( 'alpha2' => 'IS', 'name' => 'Iceland', 'alpha3' => 'ISL', 'numeric' => '352' ),
  array( 'alpha2' => 'IN', 'name' => 'India', 'alpha3' => 'IND', 'numeric' => '356' ),
  array( 'alpha2' => 'ID', 'name' => 'Indonesia', 'alpha3' => 'IDN', 'numeric' => '360' ),
  array( 'alpha2' => 'IR', 'name' => 'Iran, Islamic Republic of', 'alpha3' => 'IRN', 'numeric' => '364' ),
  array( 'alpha2' => 'IQ', 'name' => 'Iraq', 'alpha3' => 'IRQ', 'numeric' => '368' ),
  array( 'alpha2' => 'IE', 'name' => 'Ireland', 'alpha3' => 'IRL', 'numeric' => '372' ),
  array( 'alpha2' => 'IM', 'name' => 'Isle Of Man', 'alpha3' => 'IMN', 'numeric' => '833' ),
  array( 'alpha2' => 'IL', 'name' => 'Israel', 'alpha3' => 'ISR', 'numeric' => '376' ),
  array( 'alpha2' => 'IT', 'name' => 'Italy', 'alpha3' => 'ITA', 'numeric' => '380' ),
  array( 'alpha2' => 'JM', 'name' => 'Jamaica', 'alpha3' => 'JAM', 'numeric' => '388' ),
  array( 'alpha2' => 'JP', 'name' => 'Japan', 'alpha3' => 'JPN', 'numeric' => '392' ),
  array( 'alpha2' => 'JE', 'name' => 'Jersey', 'alpha3' => 'JEY', 'numeric' => '832' ),
  array( 'alpha2' => 'JO', 'name' => 'Jordan', 'alpha3' => 'JOR', 'numeric' => '400' ),
  array( 'alpha2' => 'KZ', 'name' => 'Kazakhstan', 'alpha3' => 'KAZ', 'numeric' => '398' ),
  array( 'alpha2' => 'KE', 'name' => 'Kenya', 'alpha3' => 'KEN', 'numeric' => '404' ),
  array( 'alpha2' => 'KI', 'name' => 'Kiribati', 'alpha3' => 'KIR', 'numeric' => '296' ),
  array( 'alpha2' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of', 'alpha3' => 'PRK', 'numeric' => '408' ),
  array( 'alpha2' => 'KR', 'name' => 'Korea, Republic of', 'alpha3' => 'KOR', 'numeric' => '410' ),
  array( 'alpha2' => 'KW', 'name' => 'Kuwait', 'alpha3' => 'KWT', 'numeric' => '414' ),
  array( 'alpha2' => 'KG', 'name' => 'Kyrgyzstan', 'alpha3' => 'KGZ', 'numeric' => '417' ),
  array( 'alpha2' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'alpha3' => 'LAO', 'numeric' => '418' ),
  array( 'alpha2' => 'LV', 'name' => 'Latvia', 'alpha3' => 'LVA', 'numeric' => '428' ),
  array( 'alpha2' => 'LB', 'name' => 'Lebanon', 'alpha3' => 'LBN', 'numeric' => '422' ),
  array( 'alpha2' => 'LS', 'name' => 'Lesotho', 'alpha3' => 'LSO', 'numeric' => '426' ),
  array( 'alpha2' => 'LR', 'name' => 'Liberia', 'alpha3' => 'LBR', 'numeric' => '430' ),
  array( 'alpha2' => 'LY', 'name' => 'Libyan Arab Jamahiriya', 'alpha3' => 'LBY', 'numeric' => '434' ),
  array( 'alpha2' => 'LI', 'name' => 'Liechtenstein', 'alpha3' => 'LIE', 'numeric' => '438' ),
  array( 'alpha2' => 'LT', 'name' => 'Lithuania', 'alpha3' => 'LTU', 'numeric' => '440' ),
  array( 'alpha2' => 'LU', 'name' => 'Luxembourg', 'alpha3' => 'LUX', 'numeric' => '442' ),
  array( 'alpha2' => 'MO', 'name' => 'Macao', 'alpha3' => 'MAC', 'numeric' => '446' ),
  array( 'alpha2' => 'MK', 'name' => 'Macedonia, the Former Yugoslav Republic of', 'alpha3' => 'MKD', 'numeric' => '807' ),
  array( 'alpha2' => 'MG', 'name' => 'Madagascar', 'alpha3' => 'MDG', 'numeric' => '450' ),
  array( 'alpha2' => 'MW', 'name' => 'Malawi', 'alpha3' => 'MWI', 'numeric' => '454' ),
  array( 'alpha2' => 'MY', 'name' => 'Malaysia', 'alpha3' => 'MYS', 'numeric' => '458' ),
  array( 'alpha2' => 'MV', 'name' => 'Maldives', 'alpha3' => 'MDV', 'numeric' => '462' ),
  array( 'alpha2' => 'ML', 'name' => 'Mali', 'alpha3' => 'MLI', 'numeric' => '466' ),
  array( 'alpha2' => 'MT', 'name' => 'Malta', 'alpha3' => 'MLT', 'numeric' => '470' ),
  array( 'alpha2' => 'MH', 'name' => 'Marshall Islands', 'alpha3' => 'MHL', 'numeric' => '584' ),
  array( 'alpha2' => 'MQ', 'name' => 'Martinique', 'alpha3' => 'MTQ', 'numeric' => '474' ),
  array( 'alpha2' => 'MR', 'name' => 'Mauritania', 'alpha3' => 'MRT', 'numeric' => '478' ),
  array( 'alpha2' => 'MU', 'name' => 'Mauritius', 'alpha3' => 'MUS', 'numeric' => '480' ),
  array( 'alpha2' => 'YT', 'name' => 'Mayotte', 'alpha3' => 'MYT', 'numeric' => '175' ),
  array( 'alpha2' => 'MX', 'name' => 'Mexico', 'alpha3' => 'MEX', 'numeric' => '484' ),
  array( 'alpha2' => 'FM', 'name' => 'Micronesia, Federated States of', 'alpha3' => 'FSM', 'numeric' => '583' ),
  array( 'alpha2' => 'MD', 'name' => 'Moldova, Republic of', 'alpha3' => 'MDA', 'numeric' => '498' ),
  array( 'alpha2' => 'MC', 'name' => 'Monaco', 'alpha3' => 'MCO', 'numeric' => '492' ),
  array( 'alpha2' => 'MN', 'name' => 'Mongolia', 'alpha3' => 'MNG', 'numeric' => '496' ),
  array( 'alpha2' => 'ME', 'name' => 'Montenegro', 'alpha3' => 'MNE', 'numeric' => '499' ),
  array( 'alpha2' => 'MS', 'name' => 'Montserrat', 'alpha3' => 'MSR', 'numeric' => '500' ),
  array( 'alpha2' => 'MA', 'name' => 'Morocco', 'alpha3' => 'MAR', 'numeric' => '504' ),
  array( 'alpha2' => 'MZ', 'name' => 'Mozambique', 'alpha3' => 'MOZ', 'numeric' => '508' ),
  array( 'alpha2' => 'MM', 'name' => 'Myanmar', 'alpha3' => 'MMR', 'numeric' => '104' ),
  array( 'alpha2' => 'NA', 'name' => 'Namibia', 'alpha3' => 'NAM', 'numeric' => '516' ),
  array( 'alpha2' => 'NR', 'name' => 'Nauru', 'alpha3' => 'NRU', 'numeric' => '520' ),
  array( 'alpha2' => 'NP', 'name' => 'Nepal', 'alpha3' => 'NPL', 'numeric' => '524' ),
  array( 'alpha2' => 'NL', 'name' => 'Netherlands', 'alpha3' => 'NLD', 'numeric' => '528' ),
  array( 'alpha2' => 'AN', 'name' => 'Netherlands Antilles', 'alpha3' => 'ANT', 'numeric' => '530' ),
  array( 'alpha2' => 'NC', 'name' => 'New Caledonia', 'alpha3' => 'NCL', 'numeric' => '540' ),
  array( 'alpha2' => 'NZ', 'name' => 'New Zealand', 'alpha3' => 'NZL', 'numeric' => '554' ),
  array( 'alpha2' => 'NI', 'name' => 'Nicaragua', 'alpha3' => 'NIC', 'numeric' => '558' ),
  array( 'alpha2' => 'NE', 'name' => 'Niger', 'alpha3' => 'NER', 'numeric' => '562' ),
  array( 'alpha2' => 'NG', 'name' => 'Nigeria', 'alpha3' => 'NGA', 'numeric' => '566' ),
  array( 'alpha2' => 'NU', 'name' => 'Niue', 'alpha3' => 'NIU', 'numeric' => '570' ),
  array( 'alpha2' => 'NF', 'name' => 'Norfolk Island', 'alpha3' => 'NFK', 'numeric' => '574' ),
  array( 'alpha2' => 'MP', 'name' => 'Northern Mariana Islands', 'alpha3' => 'MNP', 'numeric' => '580' ),
  array( 'alpha2' => 'NO', 'name' => 'Norway', 'alpha3' => 'NOR', 'numeric' => '578' ),
  array( 'alpha2' => 'OM', 'name' => 'Oman', 'alpha3' => 'OMN', 'numeric' => '512' ),
  array( 'alpha2' => 'PK', 'name' => 'Pakistan', 'alpha3' => 'PAK', 'numeric' => '586' ),
  array( 'alpha2' => 'PW', 'name' => 'Palau', 'alpha3' => 'PLW', 'numeric' => '585' ),
  array( 'alpha2' => 'PS', 'name' => 'Palestinian Territory, Occupied', 'alpha3' => 'PSE', 'numeric' => '275' ),
  array( 'alpha2' => 'PA', 'name' => 'Panama', 'alpha3' => 'PAN', 'numeric' => '591' ),
  array( 'alpha2' => 'PG', 'name' => 'Papua New Guinea', 'alpha3' => 'PNG', 'numeric' => '598' ),
  array( 'alpha2' => 'PY', 'name' => 'Paraguay', 'alpha3' => 'PRY', 'numeric' => '600' ),
  array( 'alpha2' => 'PE', 'name' => 'Peru', 'alpha3' => 'PER', 'numeric' => '604' ),
  array( 'alpha2' => 'PH', 'name' => 'Philippines', 'alpha3' => 'PHL', 'numeric' => '608' ),
  array( 'alpha2' => 'PN', 'name' => 'Pitcairn', 'alpha3' => 'PCN', 'numeric' => '612' ),
  array( 'alpha2' => 'PL', 'name' => 'Poland', 'alpha3' => 'POL', 'numeric' => '616' ),
  array( 'alpha2' => 'PT', 'name' => 'Portugal', 'alpha3' => 'PRT', 'numeric' => '620' ),
  array( 'alpha2' => 'PR', 'name' => 'Puerto Rico', 'alpha3' => 'PRI', 'numeric' => '630' ),
  array( 'alpha2' => 'QA', 'name' => 'Qatar', 'alpha3' => 'QAT', 'numeric' => '634' ),
  array( 'alpha2' => 'RE', 'name' => 'Reunion', 'alpha3' => 'REU', 'numeric' => '638' ),
  array( 'alpha2' => 'RO', 'name' => 'Romania', 'alpha3' => 'ROM', 'numeric' => '642' ),
  array( 'alpha2' => 'RU', 'name' => 'Russian Federation', 'alpha3' => 'RUS', 'numeric' => '643' ),
  array( 'alpha2' => 'RW', 'name' => 'Rwanda', 'alpha3' => 'RWA', 'numeric' => '646' ),
  array( 'alpha2' => 'BL', 'name' => 'Saint Barthélemy', 'alpha3' => 'BLM', 'numeric' => '652' ),
  array( 'alpha2' => 'SH', 'name' => 'Saint Helena', 'alpha3' => 'SHN', 'numeric' => '654' ),
  array( 'alpha2' => 'KN', 'name' => 'Saint Kitts and Nevis', 'alpha3' => 'KNA', 'numeric' => '659' ),
  array( 'alpha2' => 'LC', 'name' => 'Saint Lucia', 'alpha3' => 'LCA', 'numeric' => '662' ),
  array( 'alpha2' => 'MF', 'name' => 'Saint Martin (French part)', 'alpha3' => 'MAF', 'numeric' => '663' ),
  array( 'alpha2' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'alpha3' => 'SPM', 'numeric' => '666' ),
  array( 'alpha2' => 'VC', 'name' => 'Saint Vincent and the Grenadines', 'alpha3' => 'VCT', 'numeric' => '670' ),
  array( 'alpha2' => 'WS', 'name' => 'Samoa', 'alpha3' => 'WSM', 'numeric' => '882' ),
  array( 'alpha2' => 'SM', 'name' => 'San Marino', 'alpha3' => 'SMR', 'numeric' => '674' ),
  array( 'alpha2' => 'ST', 'name' => 'Sao Tome and Principe', 'alpha3' => 'STP', 'numeric' => '678' ),
  array( 'alpha2' => 'SA', 'name' => 'Saudi Arabia', 'alpha3' => 'SAU', 'numeric' => '682' ),
  array( 'alpha2' => 'SN', 'name' => 'Senegal', 'alpha3' => 'SEN', 'numeric' => '686' ),
  array( 'alpha2' => 'RS', 'name' => 'Serbia', 'alpha3' => 'SRB', 'numeric' => '688' ),
  array( 'alpha2' => 'SC', 'name' => 'Seychelles', 'alpha3' => 'SYC', 'numeric' => '690' ),
  array( 'alpha2' => 'SL', 'name' => 'Sierra Leone', 'alpha3' => 'SLE', 'numeric' => '694' ),
  array( 'alpha2' => 'SG', 'name' => 'Singapore', 'alpha3' => 'SGP', 'numeric' => '702' ),
  array( 'alpha2' => 'SK', 'name' => 'Slovakia', 'alpha3' => 'SVK', 'numeric' => '703' ),
  array( 'alpha2' => 'SI', 'name' => 'Slovenia', 'alpha3' => 'SVN', 'numeric' => '705' ),
  array( 'alpha2' => 'SB', 'name' => 'Solomon Islands', 'alpha3' => 'SLB', 'numeric' => '090' ),
  array( 'alpha2' => 'SO', 'name' => 'Somalia', 'alpha3' => 'SOM', 'numeric' => '706' ),
  array( 'alpha2' => 'ZA', 'name' => 'South Africa', 'alpha3' => 'ZAF', 'numeric' => '710' ),
  array( 'alpha2' => 'GS', 'name' => 'South Georgia and the South Sandwich Islands', 'alpha3' => 'SGS', 'numeric' => '239' ),
  array( 'alpha2' => 'ES', 'name' => 'Spain', 'alpha3' => 'ESP', 'numeric' => '724' ),
  array( 'alpha2' => 'LK', 'name' => 'Sri Lanka', 'alpha3' => 'LKA', 'numeric' => '144' ),
  array( 'alpha2' => 'SD', 'name' => 'Sudan', 'alpha3' => 'SDN', 'numeric' => '736' ),
  array( 'alpha2' => 'SR', 'name' => 'Suriname', 'alpha3' => 'SUR', 'numeric' => '740' ),
  array( 'alpha2' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'alpha3' => 'SJM', 'numeric' => '744' ),
  array( 'alpha2' => 'SZ', 'name' => 'Swaziland', 'alpha3' => 'SWZ', 'numeric' => '748' ),
  array( 'alpha2' => 'SE', 'name' => 'Sweden', 'alpha3' => 'SWE', 'numeric' => '752' ),
  array( 'alpha2' => 'CH', 'name' => 'Switzerland', 'alpha3' => 'CHE', 'numeric' => '756' ),
  array( 'alpha2' => 'SY', 'name' => 'Syrian Arab Republic', 'alpha3' => 'SYR', 'numeric' => '760' ),
  array( 'alpha2' => 'TW', 'name' => 'Taiwan, Province of China', 'alpha3' => 'TWN', 'numeric' => '158' ),
  array( 'alpha2' => 'TJ', 'name' => 'Tajikistan', 'alpha3' => 'TJK', 'numeric' => '762' ),
  array( 'alpha2' => 'TZ', 'name' => 'Tanzania, United Republic of', 'alpha3' => 'TZA', 'numeric' => '834' ),
  array( 'alpha2' => 'TH', 'name' => 'Thailand', 'alpha3' => 'THA', 'numeric' => '764' ),
  array( 'alpha2' => 'TL', 'name' => 'Timor Leste', 'alpha3' => 'TLS', 'numeric' => '626' ),
  array( 'alpha2' => 'TG', 'name' => 'Togo', 'alpha3' => 'TGO', 'numeric' => '768' ),
  array( 'alpha2' => 'TK', 'name' => 'Tokelau', 'alpha3' => 'TKL', 'numeric' => '772' ),
  array( 'alpha2' => 'TO', 'name' => 'Tonga', 'alpha3' => 'TON', 'numeric' => '776' ),
  array( 'alpha2' => 'TT', 'name' => 'Trinidad and Tobago', 'alpha3' => 'TTO', 'numeric' => '780' ),
  array( 'alpha2' => 'TN', 'name' => 'Tunisia', 'alpha3' => 'TUN', 'numeric' => '788' ),
  array( 'alpha2' => 'TR', 'name' => 'Turkey', 'alpha3' => 'TUR', 'numeric' => '792' ),
  array( 'alpha2' => 'TM', 'name' => 'Turkmenistan', 'alpha3' => 'TKM', 'numeric' => '795' ),
  array( 'alpha2' => 'TC', 'name' => 'Turks and Caicos Islands', 'alpha3' => 'TCA', 'numeric' => '796' ),
  array( 'alpha2' => 'TV', 'name' => 'Tuvalu', 'alpha3' => 'TUV', 'numeric' => '798' ),
  array( 'alpha2' => 'UG', 'name' => 'Uganda', 'alpha3' => 'UGA', 'numeric' => '800' ),
  array( 'alpha2' => 'UA', 'name' => 'Ukraine', 'alpha3' => 'UKR', 'numeric' => '804' ),
  array( 'alpha2' => 'AE', 'name' => 'United Arab Emirates', 'alpha3' => 'ARE', 'numeric' => '784' ),
  array( 'alpha2' => 'GB', 'name' => 'United Kingdom', 'alpha3' => 'GBR', 'numeric' => '826' ),
  array( 'alpha2' => 'US', 'name' => 'United States', 'alpha3' => 'USA', 'numeric' => '840' ),
  array( 'alpha2' => 'UM', 'name' => 'United States Minor Outlying Islands', 'alpha3' => 'UMI', 'numeric' => '581' ),
  array( 'alpha2' => 'UY', 'name' => 'Uruguay', 'alpha3' => 'URY', 'numeric' => '858' ),
  array( 'alpha2' => 'UZ', 'name' => 'Uzbekistan', 'alpha3' => 'UZB', 'numeric' => '860' ),
  array( 'alpha2' => 'VU', 'name' => 'Vanuatu', 'alpha3' => 'VUT', 'numeric' => '548' ),
  array( 'alpha2' => 'VE', 'name' => 'Venezuela', 'alpha3' => 'VEN', 'numeric' => '862' ),
  array( 'alpha2' => 'VN', 'name' => 'Viet Nam', 'alpha3' => 'VNM', 'numeric' => '704' ),
  array( 'alpha2' => 'VG', 'name' => 'Virgin Islands, British', 'alpha3' => 'VGB', 'numeric' => '092' ),
  array( 'alpha2' => 'VI', 'name' => 'Virgin Islands, U.S.', 'alpha3' => 'VIR', 'numeric' => '850' ),
  array( 'alpha2' => 'WF', 'name' => 'Wallis and Futuna', 'alpha3' => 'WLF', 'numeric' => '876' ),
  array( 'alpha2' => 'EH', 'name' => 'Western Sahara', 'alpha3' => 'ESH', 'numeric' => '732' ),
  array( 'alpha2' => 'YE', 'name' => 'Yemen', 'alpha3' => 'YEM', 'numeric' => '887' ),
  array( 'alpha2' => 'ZM', 'name' => 'Zambia', 'alpha3' => 'ZMB', 'numeric' => '894' ),
  array( 'alpha2' => 'ZW', 'name' => 'Zimbabwe', 'alpha3' => 'ZWE', 'numeric' => '716' )
  );

  public function __construct($options = array()) {
    required_options('name, alpha2, alpha3, numeric', $options);
    $this->name =$options['name'];
    unset($options['name']);
    foreach ( $options as $k=>$v ) {
      $this->codes[] = new Merchant_CountryCode($v);
    }
  }

  public function code($format) {
    foreach ( $this->codes as $code ) {
      if ( $code->format() == $format ) {
        return $code;
      }
    }
  }

  public function  __toString() {
    return $this->name;
  }

  public static function find($name) {
    if ( empty($name) )
      throw new Exception('Cannot lookup country for an empty name');

    if (strlen($name) == 2 || strlen($name) == 3 ) {
      $upcase_name = strtoupper($name);
      $country_code = new Merchant_CountryCode($name);
      $country_format = $country_code->format();
      foreach ( self::$COUNTRIES as $c ) {
        if ( $c[$country_format] == $upcase_name ) {
          $country = $c;
          break;
        }
      }
    } else {
      foreach ( self::$COUNTRIES as $c ) {
        if ( $c['name'] == $name ) {
          $country = $c;
          break;
        }
      }
    }
    if ( !isset($country))
      throw new Exception("No country could be found for name {$name}");

      return new Merchant_Country($country);
  }

}

class Merchant_CountryCode {
  protected $value;
  protected $format;

  public function __construct($value) {
    $this->value = strtoupper($value);
    $this->detect_format();
  }

  private function detect_format() {
    if ( preg_match('/^[[:alpha:]]{2}$/',$this->value) ) {
      $this->format = 'alpha2';
    }elseif( preg_match('/^[[:alpha:]]{3}$/',$this->value) ) {
      $this->format = 'alpha3';
    }elseif ( preg_match('/^[[:digit:]]{3}$/',$this->value) ) {
      $this->format = 'numeric';
    } else {
      throw new Exception ("The country code is not formatted correctly {$this->value}");
    }
  }

  public function format() {
    return $this->format;
  }

  public function  __toString() {
    return $this->value;
  }
}

/**
 * RequiresParameters
 * @param string comma seperated parameters. Represent keys of $options array
 * @param array the key/value hash of options to compare with
 */
function required_options($required, $options = array()) {
  $required = explode(',', $required);
  foreach ($required as $r) {
    if (!array_key_exists(trim($r), $options)) {
      throw new Exception($r . " parameter is required!");
      break;
      return false;
    }
  }
  return true;
}
?>
