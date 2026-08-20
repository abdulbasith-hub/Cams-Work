<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DgaModel extends Model
{
    public static function departments(): array
    {
        return [
            'lfa' => [
                'slug' => 'lfa',
                'short_name' => 'LFA',
                'name' => 'Local Fund Audit Department',
                'chapter' => 'Chapter 3',
                'source_file' => 'LFA (1).docx',
                'summary' => 'Annual audit of local bodies and other institutions under the Tamil Nadu Local Fund Audit Act, 2014, including audit paras, local body pension proposals, and charitable endowment treasury responsibilities.',
                'accent' => '#0b5fa5',
                'establishment' => [
                    'Began as a separate department through G.O.Ms.No.609, Finance Department, dated 22.03.1880.',
                    'Placed under the administrative control of the District Collector through G.O.Ms.No.514, Finance Department, dated 10.03.1885, and functioned under that control from 1885 to 1920.',
                    'Brought under the administrative control of the Finance Department through G.O.Ms.No.125, Finance Department, dated 10.02.1921.',
                    'The Examiner of Local Fund Accounts was appointed as Head of Department through G.O.Ms.No.1016, Finance Department, dated 27.11.1922.',
                    'Renamed as the Directorate of Local Fund Audit through G.O.Ms.No.848, Finance (LF) Department, dated 09.11.1995.',
                    'Brought under the Director General of Audit through G.O.(Ms).No.102, Finance (LF) Department, dated 07.04.2022.',
                ],
                'acts' => [
                    'Tamil Nadu Local Fund Audit Act, 2014',
                    'Tamil Nadu Local Fund Audit Rules, 2016',
                    'The Tamil Nadu Local Fund Audit Department Manual, Volume II, corrected up to 31.12.2008',
                ],
                'functions' => [
                    'Conduct annual audit of local body institutions covered by the Tamil Nadu Local Fund Audit Act, 2014.',
                    'Discharge Treasurer of Charitable Endowment responsibilities for 4 Central Endowments valued at Rs.33.74 lakhs and 898 State Endowments valued at Rs.48.31 crores.',
                    'Process pension proposals for local body employees of Municipalities, Panchayat Unions, and Town Panchayats.',
                    'Raise audit paras and pursue settlement of audit objections in Local Body Institutions and other Miscellaneous Institutions.',
                ],
                'map' => [
                    'Director General of Audit / Director of Local Fund Audit',
                    'Special Director',
                    'Joint Director (1) H.Q and Deputy Directors (2) H.Q',
                    'Regional Joint Directors (7) and Joint Director, Greater Chennai Corporation (1)',
                    'Deputy Directors (16) and District Level Assistant Directors',
                    'Inspectors, Deputy Inspectors, Assistant Inspectors',
                    'Record Clerk / Office Assistant and Junior Assistant / Typist / RA',
                ],
                'structure_map' => [
                    'root' => [
                        'title' => 'Director General of Audit / Director of Local Fund Audit',
                        'detail' => 'Administrative and supervisory head. The Director General of Audit is designated as ex-officio Director of Local Fund Audit under Section 3 of the Local Fund Audit Act, 2014.',
                    ],
                    'lead' => [
                        'title' => 'Special Director',
                        'detail' => 'Thiru. R. Dhayalan, Joint Secretary to Government, Finance Department / Special Director of Local Fund Audit. Phone: 044-24342276.',
                        'people' => [
                            [
                                'sl' => '1',
                                'name' => 'Thiru. R. Dhayalan',
                                'designation' => 'Joint Secretary to Government, Finance Department / Special Director of Local Fund Audit',
                                'address' => 'Directorate of Local Fund Audit, 571 Anna Salai, 4th Floor, Perasiriyar K. Anbalagan Maligai, Nandanam, Chennai - 600 035',
                                'phone' => '044-24342276',
                                'email' => 'tnlfa@yahoo.co.in',
                            ],
                        ],
                    ],
                    'left' => [
                        [
                            'title' => 'Joint Director (1) H.Q',
                            'detail' => 'Thiru. K. Palanivelrajan, Joint Director of Local Fund Audit (Headquarters). Phone: 044-24327211 / 044-24342276. Email: tnlfa@yahoo.co.in.',
                            'people' => [
                                [
                                    'sl' => '1',
                                    'name' => 'K. Palanivelrajan',
                                    'designation' => 'Joint Director',
                                    'address' => 'O/o Director, Local Fund Audit Department, Perasiriyar K. Anbazhagan Maligai, 4th Floor, Nandanam, Chennai - 600 035',
                                    'phone' => '044-24327211 / 044-24342276',
                                    'email' => 'tnlfa@yahoo.co.in',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Deputy Directors (2) H.Q',
                            'detail' => 'Headquarters Deputy Director posts. The source file lists Tmt. E.G. Chamundeeswari as Deputy Director of Local Fund Audit (Admin).',
                            'people' => [
                                [
                                    'sl' => '1',
                                    'name' => 'Tmt. E.G. Chamundeeswari',
                                    'designation' => 'Deputy Director of Local Fund Audit (Admin)',
                                    'address' => 'Directorate of Local Fund Audit, 571 Anna Salai, 4th Floor, Perasiriyar K. Anbalagan Maligai, Nandanam, Chennai - 600 035',
                                    'phone' => '044-24321196 / 044-24323263',
                                    'email' => 'tnlfa@yahoo.co.in',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Assistant Directors (7) H.Q',
                            'detail' => 'Seven Assistant Director posts at Headquarters as shown in the source organizational structure.',
                        ],
                    ],
                    'right' => [
                        [
                            'title' => 'Regional Joint Director (7) and Joint Director, Greater Chennai Corporation (1)',
                            'detail' => 'Regional/GCC officers listed in the source include D. Neelavathi, P. Sundara Thanga Rajeswari, V. Muthukrishnan, M. Bharathimalar, M. Maridasan, E. Shobana, R. Rajendran, and M. Krishnamurthy.',
                            'people' => self::lfaRegionalContacts(),
                        ],
                        [
                            'title' => 'Deputy Director (16)',
                            'detail' => 'Sixteen Deputy Director posts supporting regional and district audit administration as shown in the source organizational structure.',
                        ],
                        [
                            'title' => 'District Level (Assistant Directors)',
                            'detail' => 'District-level Assistant Directors are listed in the source contact table for audit offices across the State.',
                            'people' => self::lfaDistrictAssistantDirectorContacts(),
                        ],
                    ],
                    'common' => [
                        [
                            'title' => 'Inspectors',
                            'detail' => 'Field audit supervision layer connected from both Headquarters and Regional/District streams.',
                        ],
                        [
                            'title' => 'Deputy Inspectors',
                            'detail' => 'Field audit support cadre under Inspectors.',
                        ],
                        [
                            'title' => 'Assistant Inspectors',
                            'detail' => 'Assistant Inspector cadre supporting annual local body audit work.',
                        ],
                        [
                            'title' => 'Junior Assistant / Typist / RA',
                            'detail' => 'Administrative and record-assistance staff shown in the source organizational structure.',
                        ],
                        [
                            'title' => 'Record Clerk / OA',
                            'detail' => 'Record Clerk / Office Assistant support layer shown at the base of the source organizational structure.',
                        ],
                    ],
                ],
                'institutions' => [
                    ['label' => 'Total Auditable Institutions', 'value' => '4229'],
                    ['label' => 'City Municipal Corporations', 'value' => '25'],
                    ['label' => 'Municipalities', 'value' => '137'],
                    ['label' => 'Town Panchayats', 'value' => '487'],
                    ['label' => 'District Panchayats', 'value' => '36'],
                    ['label' => 'Panchayat Unions', 'value' => '388'],
                    ['label' => 'Village Panchayats', 'value' => '3030'],
                    ['label' => 'Universities', 'value' => '23'],
                    ['label' => 'Local Library Authorities', 'value' => '33'],
                    ['label' => 'Local Planning Authorities', 'value' => '27'],
                    ['label' => 'Agricultural Marketing Committee', 'value' => '32'],
                    ['label' => 'Other Institutions', 'value' => '11'],
                ],
                'achievements' => [
                    '7,26,560 audit paras amounting to Rs.1,00,27,888.34 lakhs were raised during the last five years.',
                    '5,87,796 audit paras amounting to Rs.30,66,832.51 lakhs were settled during the last five years.',
                    'Pensionary benefits were settled for 6,877 pensioners, amounting to Rs.87,654.67 lakhs, during the last five years.',
                    '306 Assistant Inspectors, 31 Junior Assistants, 51 Typists, and 2 Steno Typists were recruited through direct recruitment.',
                    '22 Junior Assistants and 1 Office Assistant were recruited on compassionate grounds.',
                ],
                'contact' => [
                    'Directorate of Local Fund Audit (Head Office), 571 Anna Salai, 4th Floor, Perasiriyar K. Anbalagan Maligai, Nandanam, Chennai - 600 035, Tamil Nadu.',
                    'Thiru. R. Dhayalan, Joint Secretary to Government, Finance Department / Special Director of Local Fund Audit - 044-24342276',
                    'Thiru. K. Palanivelrajan, Joint Director of Local Fund Audit (Headquarters)',
                    'Tmt. E.G. Chamundeeswari, Deputy Director of Local Fund Audit (Admin)',
                    'Phone: 044-24321196, 044-24323263',
                    'Email: tnlfa@yahoo.co.in',
                ],
            ],
            'sgad' => [
                'slug' => 'sgad',
                'short_name' => 'SGAD',
                'name' => 'State Government Audit Department',
                'chapter' => 'Chapter 4',
                'source_file' => 'SGAD (4).docx',
                'summary' => 'Statutory and internal audit of statutory boards, non-statutory boards, Government departments, Government-aided institutions, and audit reports requiring Government and Public Undertakings Committee attention.',
                'accent' => '#027c86',
                'establishment' => [
                    'In 1945, the Examiner of Local Fund Accounts was appointed as Chief Auditor, State Trading Schemes, for internal departmental audit of State-run trading schemes.',
                    'A separate department for audit of State Trading Schemes was formed through G.O.Ms.No.449, Finance, dated 03.05.1969.',
                    'The Chief Auditor, State Trading Schemes was redesignated as Chief Internal Auditor and Chief Auditor of Statutory Boards through G.O.Ms.No.598, Finance (Local Fund) Department, dated 03.08.1992.',
                    'The department was renamed as Internal Audit and Statutory Boards Audit Department through G.O.Ms.No.848, Finance (LF) Department, dated 09.11.1995.',
                    'The department was renamed as the State Government Audit Department through G.O.Ms.No.264, Finance (LF) Department, dated 16.08.2019.',
                ],
                'acts' => [
                    'Tamil Nadu Local Fund Audit Act, 2014',
                    'Tamil Nadu Local Fund Audit Rules, 2016',
                    'The Internal Audit and Statutory Audit Department Manual, Volume III-A',
                    'The Internal Audit and Statutory Audit Department Manual, Volume III-B',
                    'The Tamil Nadu Local Fund Audit Department Manual, Volume II, corrected up to 31.12.2008',
                ],
                'functions' => [
                    'Conduct statutory and internal audit of Statutory Boards, Non-Statutory Boards, Government Departments, and Government-aided Institutions.',
                    'Issue audit reports within stipulated time frames and bring serious irregularities to the notice of Government and the Public Undertakings Committee of the Tamil Nadu Legislature.',
                    'Review internal audit functions in Government departments as authorized through G.O.Ms.No.598 dated 03.08.1992 and G.O.Ms.No.736 dated 19.09.1995.',
                    'Cover statutory audit institutions such as TNHB, TNUHDB, Khadi Board, Wakf Board, CMDA, CMWSSB, TWAD Board, and Labour Welfare Board.',
                ],
                'map' => [
                    'Director',
                    'Joint Director',
                    'Deputy Director - Head Office',
                    'Deputy Director - Boards',
                    'Deputy Director - City Audit',
                    'Assistant Directors, Inspectors, Deputy Inspectors, Assistant Inspectors and support staff',
                ],
                'structure_image' => 'site/image/dga/sgad-structure.png',
                'institutions' => [
                    ['label' => 'Total Auditable Institutions', 'value' => '6492'],
                    ['label' => 'Chennai Metropolitan Development Authority', 'value' => '1'],
                    ['label' => 'Chennai Metropolitan Water Supply and Sewerage Board', 'value' => '1'],
                    ['label' => 'Tamil Nadu Housing Board', 'value' => '27'],
                    ['label' => 'Tamil Nadu Urban Habitat Development Board', 'value' => '22'],
                    ['label' => 'Tamil Nadu Khadi and Village Industries Board', 'value' => '24'],
                    ['label' => 'Tamil Nadu Text Book and Educational Services Corporation', 'value' => '24'],
                    ['label' => 'Administrative General', 'value' => '1'],
                    ['label' => 'Official Trustee', 'value' => '1'],
                    ['label' => 'Official Assignee', 'value' => '1'],
                    ['label' => 'Official Liquidator', 'value' => '1'],
                    ['label' => 'Suitor\'s Fund', 'value' => '1'],
                    ['label' => 'Civil Court Deposit', 'value' => '1'],
                    ['label' => 'Tamil Nadu Water Supply and Drainage Board Institutions', 'value' => '124'],
                    ['label' => 'Agriculture Institutions', 'value' => '1146'],
                    ['label' => 'Government Institutions', 'value' => '167'],
                    ['label' => 'Government Aided Institutions', 'value' => '33'],
                    ['label' => 'Government Aided Arts and Science Colleges', 'value' => '162'],
                    ['label' => 'Government Aided Engineering and Polytechnic Colleges', 'value' => '35'],
                    ['label' => 'Wakf Institutions', 'value' => '2035'],
                    ['label' => 'Sarvodaya Sangam', 'value' => '70'],
                    ['label' => 'Election Audit Institutions', 'value' => '307'],
                    ['label' => 'P.T.MGR Nutritious Noon Meal Scheme', 'value' => '572'],
                    ['label' => 'Integrated Child Development Scheme', 'value' => '479'],
                    ['label' => 'Adi Dravidar Welfare Hostels', 'value' => '1208'],
                    ['label' => 'Tribal Welfare Hostels', 'value' => '49'],
                ],
                'achievements' => [
                    'Special Audit on field verification and genuineness of income certificates for BC/MBC/MW/SC/ST scholarships and First Graduate certificates was conducted and submitted to Government.',
                    'Special Audit on receipts and expenditure relating to COVID-19 in five selected districts was conducted and submitted to Government.',
                    'General, IT-related, and domain-based training programmes are conducted periodically to improve audit quality.',
                    'The e-Office system has been fully implemented in the State Government Audit Department.',
                    'Audit objection time analysis and categorisation were completed and pending audit para details were communicated to auditee institutions through email.',
                    'Approximately Rs.28.41 crores was collected as audit fees during the period from 07.05.2021 to 31.09.2025.',
                    'G.O.(Ms) No.27, Finance (LF) Department, dated 04.02.2025, constituted High Level Committees to facilitate timely settlement of audit paras.',
                ],
                'contact' => [
                    'Directorate of State Government Audit Department, 571 Anna Salai, 4th Floor, Perasiriyar K. Anbalagan Maligai, Veterinary Hospital Campus, Nandanam, Chennai - 600 035, Tamil Nadu.',
                    'Thiru. H. Abdullah Kaadhar, Director of State Government Audit Department - 044-24360607',
                    'Thiru. R. Pandiyan, Joint Director of State Government Audit Department - 044-24353506',
                    'Tmt. D. Shanthi, Deputy Director of State Government Audit Department - 044-24357606',
                    'Thiru. R. Kothairaman, Assistant Director of State Government Audit Department - 044-24353506',
                    'Thiru. S. Dilli, Assistant Director of State Government Audit Department (Audit) - 044-24353507',
                    'Phone: 044-24350605',
                    'Email: cia.casbs@tn.gov.in, sgadho2019@gmail.com',
                ],
            ],
            'hria' => [
                'slug' => 'hria',
                'short_name' => 'HRIA',
                'name' => 'Hindu Religious Institutions Audit Department',
                // 'chapter' => 'Chapter 5',
                // 'source_file' => 'HRIA.docx',
                'summary' => 'Audit of Hindu religious institutions, template audit for smaller institutions, and performance audit of selected functional areas.',
                'accent' => '#8a5b05',
                'establishment' => [
                    'Independent audit wing established through G.O.(Ms.) No.1826 dated 21.06.1972.',
                    'Relieved from HR&CE Department and constituted under Finance Department through G.O.Ms.No.181 dated 25.11.2021.',
                    'Brought under the Director General of Audit through G.O.Ms.No.102 dated 07.04.2022.',
                    'Head of Department post created through G.O.Ms.No.129 dated 13.05.2022; department functioning from 01.06.2022.',
                ],
                'acts' => [
                    'Tamil Nadu Hindu Religious and Charitable Endowments Act, 1959',
                    'Tamil Nadu Hindu Religious and Charitable Endowments Rules, 1959',
                    'Tamil Nadu Hindu Religious Institutions Employees Conditions of Service Rules, 2020',
                ],
                'functions' => [
                    'Formulate audit procedures and fix quarterly audit programmes through CAMS.',
                    'Monitor audit progress and approve special audit programmes.',
                    'Forward serious irregularity reports to the Commissioner of HR&CE Department.',
                    'Monitor settlement of audit paras through APMS.',
                ],
                'map' => [
                    'Director General of Audit',
                    'Director',
                    'Directorate and Regional Offices',
                    'Chief Audit Officer, Deputy Chief Audit Officers and Regional Audit Officers',
                    'Assistant Audit Officers and Audit Superintendents',
                    'Audit Inspectors',
                ],
                'institutions' => [
                    ['label' => 'Regular Audit Institutions', 'value' => '4204'],
                    ['label' => 'Template Audit Institutions', 'value' => '21675'],
                    ['label' => 'Total Audit Institutions', 'value' => '25879'],
                    ['label' => 'Category 1 above 1 crore', 'value' => '179'],
                    ['label' => 'Category 2 above 50 lakh to 1 crore', 'value' => '110'],
                    ['label' => 'Category 3 above 10 lakh to 50 lakh', 'value' => '662'],
                    ['label' => 'Category 4 above 3 lakh to 10 lakh', 'value' => '3225'],
                    ['label' => 'Oru Kaala Poojai', 'value' => '17853'],
                ],
                'achievements' => [
                    'Six-month Combined Audit Training Programme for Audit Inspectors recruited in 2024 and 2025.',
                    'Performance Audit introduced during 2024-2025.',
                    'Performance Audit areas include Annadhanam, Temple Tank Maintenance, Property Management, and Crowd and Sanitary Management.',
                ],
                'contact' => [
                    'Directorate of Hindu Religious Institutions Audit, Head Office.',
                    'Director: Tmt. T.J. Srividhya, Phone: 044-24340874',
                    'Chief Audit Officer: Tmt. K. Geetha',
                ],
            ],
            'dca' => [
                'slug' => 'dca',
                'short_name' => 'DCA',
                'name' => 'Department of Co-operative Audit',
                // 'chapter' => 'Chapter 6',
                // 'source_file' => 'DCA (1).docx',
                'summary' => 'Independent audit of co-operative societies through a directorate, regional offices, and district audit circle offices.',
                'accent' => '#2d7b4f',
                'establishment' => [
                    'Formed as a separate Department of Co-operative Audit with effect from 17.06.1981.',
                    'Created based on Santhanam Committee recommendations and G.O.Ms.No.677 dated 22.11.1978.',
                    'Headed by the Director of Co-operative Audit under the administrative control of the Finance Department.',
                    'Director is also designated as Registrar of Co-operative Audit under Section 3 of the Tamil Nadu Co-operative Societies Act, 1983.',
                ],
                'acts' => [
                    'Tamil Nadu Co-operative Societies Act, 1983',
                    'Tamil Nadu Co-operative Societies Rules, 1988',
                ],
                'functions' => [
                    'Formulate audit procedures and fix quarterly audit plans through CAMS.',
                    'Monitor audit progress, audit fee collection, and settlement of audit paras through APMS.',
                    'Approve special audit programmes and forward serious irregularity reports.',
                    'Conduct departmental revenue audit, liquidation audit, test audits, and periodic inspections.',
                ],
                'map' => [
                    'Directorate of Co-operative Audit',
                    '7 Regional Joint Director Offices',
                    '33 Assistant Director Offices at district level',
                    'Audit programme planning and certificate issue',
                    'Audit progress monitoring and report issue',
                    'Audit fee collection, inspections, test audit and liquidation audit',
                ],
                'regions' => [
                    ['name' => 'Chennai Region', 'places' => ['Chennai North', 'Chennai South', 'Thiruvallur', 'Kanchipuram', 'Vellore']],
                    ['name' => 'Trichy Region', 'places' => ['Trichy', 'Thanjavur', 'Nagapattinam', 'Thiruvarur', 'Pudukkottai']],
                    ['name' => 'Villupuram Region', 'places' => ['Villupuram', 'Cuddalore', 'Ariyalur', 'Thiruvannamalai']],
                    ['name' => 'Salem Region', 'places' => ['Salem', 'Namakkal', 'Karur', 'Dharmapuri', 'Krishnagiri']],
                    ['name' => 'Madurai Region', 'places' => ['Madurai', 'Periyakulam', 'Ramnad', 'Sivagangai']],
                    ['name' => 'Tirunelveli Region', 'places' => ['Tirunelveli', 'Kovilpatti', 'Tuticorin', 'Virudhu Nagar', 'Nagarcoil']],
                    ['name' => 'Coimbatore Region', 'places' => ['Coimbatore', 'Erode', 'Dindugal', 'Ooty', 'Tirupur']],
                ],
                'institutions' => [
                    ['label' => 'Head Office', 'value' => '1'],
                    ['label' => 'Regional Offices', 'value' => '7'],
                    ['label' => 'District Circle Offices', 'value' => '33'],
                    ['label' => 'Total Offices', 'value' => '41'],
                ],
                'achievements' => [
                    'Three-tier management setup covering Head Office, Regional Offices, and District Circle Offices.',
                    'Regional offices are located at Chennai, Trichy, Madurai, Coimbatore, Tirunelveli, Villupuram and Salem.',
                    'District offices finalize quarterly society lists, approve programmes, monitor work diaries, levy audit fees, and conduct test audits.',
                ],
                'contact' => [],
            ],
            'milk' => [
                'slug' => 'milk',
                'short_name' => 'Milk',
                'name' => 'Department of Audit for Milk Co-operatives',
                // 'chapter' => 'Chapter 7',
                // 'source_file' => 'Milk.docx',
                'summary' => 'Statutory audit of Milk Co-operative Societies across federation, district union, and primary society levels.',
                'accent' => '#9b2f2f',
                'establishment' => [
                    'Created on 01.10.1987 through G.O.(Ms) No.1877 dated 28.09.1987 after abolition of the Audit Board.',
                    'Originally under Agriculture Department and later Animal Husbandry, Milk Production and Dairy Development Department.',
                    'Brought under Finance Department as a separate department through G.O.(Ms) No.370 dated 22.11.2019.',
                    'Cadre strength restructured to 323 posts through G.O.(Ms) No.55 dated 02.03.2026.',
                ],
                'acts' => [
                    'Tamil Nadu Co-operative Societies Act, 1983',
                    'Tamil Nadu Co-operative Societies Rules, 1988',
                ],
                'functions' => [
                    'Conduct statutory audit of all Milk Co-operative Societies in the State.',
                    'Audit the three-tier milk co-operative system at federation, district union, and primary society levels.',
                    'Classify societies for audit with reference to milk procurement.',
                    'Exercise powers under Section 80 of the Tamil Nadu Co-operative Societies Act, 1983 and Rules 101, 102, and 103.',
                ],
                'map' => [
                    'Department of Audit for Milk Co-operatives',
                    'Tamil Nadu Co-operative Milk Producers Federation',
                    'District Co-operative Milk Producers Unions',
                    'Primary Milk Producers Co-operative Societies',
                    'Milk Consumers Societies',
                ],
                'regions' => [
                    ['name' => 'Chennai', 'places' => ['Kanchipuram', 'Thiruvannamalai', 'Thiruvallur', 'Vellore', 'Chennai']],
                    ['name' => 'Erode', 'places' => ['Coimbatore', 'Dindugal', 'Erode', 'Ooty', 'Thiruppur']],
                    ['name' => 'Madurai', 'places' => ['Madurai', 'Manamadurai', 'Ramanathapuram', 'Theni', 'Thirunelveli', 'Tuticorin', 'Virudhunagar']],
                    ['name' => 'Salem', 'places' => ['Dharmapuri', 'Krishnagiri', 'Namakkal', 'Salem']],
                ],
                'institutions' => [
                    ['label' => 'Tamil Nadu Co-operative Milk Producers Federation', 'value' => '1'],
                    ['label' => 'District Co-operative Milk Producers Unions', 'value' => '27'],
                    ['label' => 'Primary Milk Producers Co-operative Societies', 'value' => '10496'],
                    ['label' => 'Restructured Cadre Strength', 'value' => '323'],
                ],
                'achievements' => [
                    'Audit coverage follows the State, district, and grassroot co-operative milk structure.',
                    'The department functions under the administrative control of the Finance Department.',
                    'Audit classification is linked to milk procurement and society type.',
                ],
                'contact' => [],
            ],
        ];
    }

    public static function departmentContent(string $slug): ?array
    {
        $departments = self::departments();

        if (!isset($departments[$slug])) {
            return null;
        }

        return self::applyPortalOverrides('department', $slug, $departments[$slug]);
    }

    private static function applyPortalOverrides(string $pageKey, ?string $pageScope, array $content): array
    {
        // Sections stored as {text: "..."} rows, flattened back to a plain string list.
        $plainTextListSections = $pageKey === 'home' ? [] : ['establishment', 'acts', 'functions', 'achievements'];
        // Sections stored as structured rows, passed through as-is.
        $structuredListSections = $pageKey === 'home' ? ['updates', 'gallery', 'downloads', 'services'] : ['institutions', 'regions', 'nav_menu', 'custom_block'];

        foreach ($plainTextListSections as $sectionKey) {
            $rows = PortalModel::publishedSection($pageKey, $pageScope, $sectionKey);
            if (!empty($rows)) {
                $content[$sectionKey] = array_map(fn ($row) => $row['text'] ?? '', $rows);
            }
        }

        foreach ($structuredListSections as $sectionKey) {
            $rows = PortalModel::publishedSection($pageKey, $pageScope, $sectionKey);
            if (!empty($rows)) {
                $content[$sectionKey] = $rows;
            }
        }

        $contact = PortalModel::publishedSingleton($pageKey, $pageScope, 'contact');
        if ($contact !== null) {
            $content['contact'] = $contact;
        }

        if ($pageKey === 'department') {
            $structureMap = PortalModel::publishedSingleton($pageKey, $pageScope, 'structure_map');
            if ($structureMap !== null) {
                $content['structure_map'] = $structureMap;
            }
        }

        return $content;
    }

    public static function homeContent(): array
    {
        $content = [
	            'hero' => [
	                'eyebrow' => 'Government of Tamil Nadu',
	                'title' => 'Office of the Director General of Audit',
	                'subtitle' => 'A unified administrative and supervisory office for strengthening internal and statutory audit functions across Government audit departments.',
	                'stats' => [
	                    ['value' => '07.04.2022', 'label' => 'DGA established through G.O.Ms.No.102, Finance (LF) Department'],
	                    ['value' => '15.04.2025', 'label' => 'CAMS implemented across Audit Departments'],
	                    ['value' => '5', 'label' => 'Major functional wings for audit quality, reform, performance, IT, and legal support'],
	                ],
	            ],
	            'updates' => [
	                ['date' => '15.04.2025', 'text' => 'CAMS implemented across Audit Departments for digital audit supervision and monitoring.'],
	                ['date' => '17.11.2025', 'text' => 'APMS-based faceless settlement introduced to improve transparency and objectivity.'],
	                ['date' => '2025-26', 'text' => 'Performance Audit, Template Audit, and training activities are strengthened through CAMS.'],
	                ['date' => 'Support', 'text' => 'CAMS helpdesk support is available for field audit and APMS issues.'],
	            ],
		            'overview_lead' => 'A unified audit command center that connects departments, standards, digital workflows, and corrective action in one accountable system.',
		            'overview' => [
	                'Brings the major Government audit directorates under one administrative view for coordinated supervision.',
	                'Links statutory audit, internal audit, quality review, performance review, legal support, and IT enablement.',
	                'Keeps audit progress, objection follow-up, and department-level coordination visible for faster action.',
	            ],
	            'mandate' => [
	                'Turn audit evidence into timely decision support for departments, schemes, and public finance oversight.',
	                'Improve transparency by moving communication, monitoring, and para settlement into traceable digital channels.',
	                'Detect lapses early, support corrective measures, and strengthen confidence in public fund management.',
	                'Build audit capacity through standard practices, training, systems support, and continuous improvement.',
	            ],
	            'flow_map' => [
	                'top' => 'Director General of Audit',
	                'headquarters' => 'Head Quarters',
	                'field_title' => 'Field Directorates',
	                'secretariat' => [
	                    'PS to DGA',
	                    'DGA Secretariat',
                ],
                'division_root' => 'Divisions',
                'divisions' => [
                    'Office Procedures and Coordination Division',
                    'Capacity Building and IT Wing',
                    'Performance Audit Wing',
                    'Audit Reforms Wing',
	                    'Audit Quality Wing',
	                    'Legal Cell Wing',
	                ],
	                'field_directorates' => [
	                    ['label' => 'Directorate of Local Audit', 'slug' => 'lfa'],
	                    ['label' => 'Directorate of State Government Audit', 'slug' => 'sgad'],
	                    ['label' => 'Directorate of Cooperative Audit', 'slug' => 'dca'],
	                    ['label' => 'Directorate of Audit for Milk Cooperatives', 'slug' => 'milk'],
	                    ['label' => 'Directorate of Hindu Religious Institutions Audit', 'slug' => 'hria'],
	                ],
	            ],
            'wings' => [
                [
                    'name' => 'Audit Quality Wing',
                    'detail' => 'Studies statutes, guidelines, Government Orders, supporting rules, Finance/Audit Committee practices, and audit standards. It also follows up audit progress, objection settlement, and categorization of pending audit objections across the five Audit Departments.',
                ],
                [
                    'name' => 'Audit Reforms Wing',
                    'detail' => 'Improves audit report quality, transparency in financial reporting, financial analysis support, and follow-up on State High-Level Committee meetings.',
                ],
                [
                    'name' => 'Performance Audit Wing',
                    'detail' => 'Coordinates performance audit planning and execution, supports audit theme selection, methodology, KPI identification, scope, best-practice study, and finalization of performance audit reports.',
                ],
                [
                    'name' => 'Capacity Building and IT Wing',
                    'detail' => 'Builds audit staff skills through structured training calendars, training needs analysis, and specialized modules. The IT section coordinates CAMS development with NIC and departments, handles technical issues, hardware/software procurement, e-office operations, SSL renewal, and security audits.',
                ],
                [
                    'name' => 'Legal Cell Wing',
                    'detail' => 'Supports audit departments in litigation, legal opinions, pleadings, court procedure, statutory compliance, RTI matters, and capacity building on litigation management and legal principles.',
                ],
            ],
            'roadmap' => [
                ['title' => 'DGA Established', 'detail' => 'Office created through G.O.Ms.No.102, Finance (LF) Department, dated 07.04.2022.'],
                ['title' => 'CAMS Approved', 'detail' => 'Administrative approval through G.O.(Ms) No.280 dated 02.09.2023 and financial sanction through G.O.(D) No.315 dated 23.09.2024.'],
                ['title' => 'Digital Audit Rollout', 'detail' => 'CAMS implemented from 15.04.2025 as the digital backbone for audit supervision and monitoring.'],
                ['title' => 'Field Audit Workflow', 'detail' => 'Digital intimation, records submission, slips, replies, para conversion, and audit report generation.'],
                ['title' => 'Faceless Settlement', 'detail' => 'APMS-based faceless settlement introduced from 17.11.2025 to improve transparency and objectivity.'],
                ['title' => 'Continuous Improvement', 'detail' => 'Performance Audit, Template Audit, training programs, and CAMS helpdesk support strengthen audit quality.'],
            ],
	            'process_map' => [
	                [
	                    'title' => 'Audit Setup',
	                    'detail' => 'Auditor profiles, roles, institution master data, and audit grouping are prepared.',
	                    'icon' => 'fas fa-cogs',
	                ],
	                [
	                    'title' => 'Plan Allocation',
	                    'detail' => 'Institutions and audit teams are selected through randomized and controlled allocation.',
	                    'icon' => 'fas fa-random',
	                ],
	                [
	                    'title' => 'Schedule & Intimation',
	                    'detail' => 'Audit dates are fixed, intimation is sent, and digital records are requested.',
	                    'icon' => 'fas fa-calendar-check',
	                ],
	                [
	                    'title' => 'Field Verification',
	                    'detail' => 'Audit slips, auditee replies, and auditor review are handled through CAMS.',
	                    'icon' => 'fas fa-clipboard-check',
	                ],
	                [
	                    'title' => 'Para Processing',
	                    'detail' => 'Slips are reviewed and converted into audit paras wherever required.',
	                    'icon' => 'fas fa-file-alt',
	                ],
	                [
	                    'title' => 'Settlement & Report',
	                    'detail' => 'APMS settlement, monitoring, and audit report generation complete the cycle.',
	                    'icon' => 'fas fa-chart-line',
	                ],
	            ],
            'reforms' => [
                'Automated and randomized selection of audit teams to support impartiality.',
                'Random selection of institutions for fair audit allocation.',
                'End-to-end digital correspondence between auditors and auditee institutions.',
                'Automated notifications for audit commencement, audit slips, and audit paras through email and SMS.',
                'Automated field audit workflow from entry meeting to conversion of slips into paras.',
                'Concurrent audit units for equitable workload distribution.',
                'Automatic generation of audit reports based on audit paras and financial records.',
            ],
            'modules' => [
                [
                    'name' => 'Auditor Management',
                    'points' => ['Auditor profile creation', 'Role and responsibility assignment', 'Charge and additional charge management'],
                ],
                [
                    'name' => 'Institution Master Management',
                    'points' => ['Call for records', 'Institution grouping', 'Randomized work allocation', 'Slip categorization'],
                ],
                [
                    'name' => 'Automated Audit Plan',
                    'points' => ['Random audit team assignment', 'Institution assignment through CAMS'],
                ],
                [
                    'name' => 'Schedule',
                    'points' => ['Auto-generated intimation letters', 'Audit team details', 'Account particulars and digital document submission'],
                ],
                [
                    'name' => 'Field Audit',
                    'points' => ['Audit slips with category, severity, and observations', 'Institution replies', 'Auditor review and conversion to audit para'],
                ],
                [
                    'name' => 'APMS Faceless Settlement',
                    'points' => ['Random assignment of para replies to reviewing authorities', 'Anonymity for unbiased decisions', 'Online settlement with weekly reviews'],
                ],
                [
                    'name' => 'Responsibility Removal',
                    'points' => ['Joint liability para review for officials nearing retirement', 'Committee-based settlement for timely resolution'],
                ],
                [
                    'name' => 'Performance and Template Audit',
                    'points' => ['Scheme and agency performance audit', 'Standardized audit formats for smaller institutions'],
                ],
            ],
            'training' => [
                'Foundation Training for new recruits',
                'Refresher Courses for serving personnel',
                'Specialized Training in Performance Audit across Audit Departments',
            ],
            'services' => [
                [
                    'icon' => 'bi-shield-check',
                    'title' => 'Digital Audit (CAMS)',
                    'description' => 'End-to-end digital audit planning, field verification, and report generation.',
                    'href' => '#modules',
                ],
                [
                    'icon' => 'bi-diagram-3',
                    'title' => 'Field Directorates',
                    'description' => 'Access LFA, SGAD, HRIA, DCA, and Milk audit department portals.',
                    'href' => '#highlights',
                ],
                [
                    'icon' => 'bi-file-earmark-text',
                    'title' => 'APMS Settlement',
                    'description' => 'Faceless, transparent settlement of audit paras with weekly reviews.',
                    'href' => '#updates',
                ],
                [
                    'icon' => 'bi-graph-up-arrow',
                    'title' => 'Performance Audit',
                    'description' => 'Theme-based performance reviews with KPIs and best-practice study.',
                    'href' => '#highlights',
                ],
                [
                    'icon' => 'bi-mortarboard',
                    'title' => 'Capacity Building',
                    'description' => 'Foundation, refresher, and specialized training for audit personnel.',
                    'href' => '#contact',
                ],
                [
                    'icon' => 'bi-download',
                    'title' => 'Downloads',
                    'description' => 'Acts, rules, manuals, and citizen-facing documents.',
                    'href' => '#downloads',
                ],
                [
                    'icon' => 'bi-bell',
                    'title' => 'Announcements',
                    'description' => 'Official notices, circulars, and implementation updates.',
                    'href' => '#announcements',
                ],
                [
                    'icon' => 'bi-headset',
                    'title' => 'Helpdesk Support',
                    'description' => 'CAMS and APMS technical assistance for field offices and HODs.',
                    'href' => '#contact',
                ],
            ],
            'announcements' => [
                'CAMS is live across Audit Departments for digital audit supervision and monitoring.',
                'APMS-based faceless settlement is operational to improve transparency and objectivity.',
                'Performance Audit, Template Audit, and training calendars are strengthened for 2025-26.',
            ],
            'statistics' => [
                ['value' => 5, 'suffix' => '', 'label' => 'Field Directorates'],
                ['value' => 5, 'suffix' => '', 'label' => 'Functional Wings'],
                ['value' => 8, 'suffix' => '+', 'label' => 'CAMS Modules'],
                ['value' => 2025, 'suffix' => '', 'label' => 'Digital Rollout Year'],
            ],
            'highlights' => [
                [
                    'title' => 'Unified Audit Command',
                    'text' => 'One supervisory office connecting statutory audit, quality review, performance audit, legal support, and IT enablement.',
                    'image' => 'assets/images/cam.jpg',
                ],
                [
                    'title' => 'Digital Workflow',
                    'text' => 'From randomized team allocation to faceless para settlement, CAMS digitizes the full audit cycle.',
                    'image' => 'site/image/hero-bg.png',
                ],
                [
                    'title' => 'Citizen Accountability',
                    'text' => 'Transparent monitoring, timely objections follow-up, and stronger confidence in public fund management.',
                    'image' => 'site/image/background.jpg',
                ],
            ],
            'gallery' => [
                ['src' => 'assets/images/cam.jpg', 'alt' => 'CAMS digital audit platform'],
                ['src' => 'assets/images/minister_photo.jpg', 'alt' => 'Official photograph'],
                ['src' => 'site/image/carsoul1.jpg', 'alt' => 'Department outreach'],
                ['src' => 'site/image/carsoul2.jpg', 'alt' => 'Governance initiative'],
                ['src' => 'site/image/carsoul3.jpg', 'alt' => 'Public service programme'],
                ['src' => 'site/image/carsoul4.jpg', 'alt' => 'State administration event'],
            ],
            'downloads' => [
                [
                    'title' => 'Tamil Nadu Local Fund Audit Act, 2014',
                    'meta' => 'PDF · Act',
                    'href' => '#',
                ],
                [
                    'title' => 'Tamil Nadu Local Fund Audit Rules, 2016',
                    'meta' => 'PDF · Rules',
                    'href' => '#',
                ],
                [
                    'title' => 'CAMS User Guidance Overview',
                    'meta' => 'PDF · Guidance',
                    'href' => '#',
                ],
                [
                    'title' => 'Performance Audit Framework Note',
                    'meta' => 'PDF · Framework',
                    'href' => '#',
                ],
            ],
            'contact' => [
                'office' => 'Office of the Director General of Audit',
                'address' => '571 Anna Salai, 4th Floor, Perasiriyar K. Anbalagan Maligai, Nandanam, Chennai - 600 035, Tamil Nadu.',
                'phone' => '044-2432 1196',
                'email' => 'cams.dga@tn.gov.in',
                'map_embed' => 'https://maps.google.com/maps?q=Perasiriyar%20K.%20Anbalagan%20Maligai%20Nandanam%20Chennai&t=&z=15&ie=UTF8&iwloc=&output=embed',
            ],
        ];

        return self::applyPortalOverrides('home', null, $content);
    }

    private static function lfaRegionalContacts(): array
    {
        return [
            ['sl' => '1', 'name' => 'D. Neelavathi', 'designation' => 'Joint Director', 'address' => 'O/o The Joint Director, Greater Chennai Corporation Audit, Chennai - 600003', 'phone' => '044-25383963', 'email' => 'jdauditcoc@gmail.com'],
            ['sl' => '2', 'name' => 'P. Sundara Thanga Rajeswari', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director, Local Fund Audit, Chengalpattu Region, No.1, Maraimalai Nagar - 603209, Chengalpattu Dist.', 'phone' => '044-27454447', 'email' => 'rjdchennai.vpm@gmail.com'],
            ['sl' => '3', 'name' => 'V. Muthukrishnan', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director, Local Fund Audit Department, Perumal Swamy Building, District Panchayat Board Campus, Officer Line, Vellore - 632001', 'phone' => '0416-2234750', 'email' => 'rjdlf.vlr@gmail.com'],
            ['sl' => '4', 'name' => 'M. Bharathimalar', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director, Local Fund Audit Department, Local Library Authority Building, Serarajen Salai, Salem - 636007', 'phone' => '0427-2413922', 'email' => 'rjdlfsalem@gmail.com'],
            ['sl' => '5', 'name' => 'M. Maridasan', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director, Local Fund Audit, 4th Floor, LLA Building, Townhall Local, Coimbatore - 641001', 'phone' => '0422-2398229', 'email' => 'rddlfcbeaudit@gmail.com'],
            ['sl' => '6', 'name' => 'E. Shobana', 'designation' => 'Regional Joint Director (i/c)', 'address' => 'O/o Regional Joint Director, Block C Corporation Complex, 1st Floor, S.N. High Road, Thirunelveli - 627001', 'phone' => '0462-2332734', 'email' => 'rddlftvl@yahoo.co.in'],
            ['sl' => '7', 'name' => 'R. Rajendran', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director, Besent Road, Baskaran Complex, Chokkikulam, Madurai - 625002', 'phone' => '0452-2522670', 'email' => 'rddmdu@yahoo.com'],
            ['sl' => '8', 'name' => 'M. Krishnamurthy', 'designation' => 'Regional Joint Director', 'address' => 'O/o Regional Joint Director Local Fund Audit, City Corporation Water Tank, 1st Floor, Opp. Vignesh Hotel, Trichy - 620001', 'phone' => '0431-2400423', 'email' => 'rddtry@yahoo.co.in'],
        ];
    }

    private static function lfaDistrictAssistantDirectorContacts(): array
    {
        return [
            ['sl' => '1', 'name' => 'Rathinakumar', 'designation' => 'Assistant Director - Cuddalore', 'address' => '13A, First floor, Bharathi Road, Cuddalore - 607001', 'phone' => '9787464051', 'email' => ''],
            ['sl' => '2', 'name' => 'Saraswathi Raman', 'designation' => 'Assistant Director - Cuddalore', 'address' => '', 'phone' => '9952591927', 'email' => ''],
            ['sl' => '3', 'name' => 'S P Kasthuri', 'designation' => 'Assistant Director - Kancheepuram', 'address' => '45, 11th Street, Gandhi Nagar, Kanchipuram - 631501', 'phone' => '9994346536', 'email' => ''],
            ['sl' => '4', 'name' => 'E Sivasankari', 'designation' => 'Assistant Director - Kancheepuram', 'address' => '', 'phone' => '9003404579', 'email' => ''],
            ['sl' => '5', 'name' => 'V. Srinivasan', 'designation' => 'Assistant Director - Tiruvallur', 'address' => '210/4 - W/MN Complex, C.V. Naidu Road, Tiruttani Road, Tiruvallur - 602001', 'phone' => '9600980554', 'email' => ''],
            ['sl' => '6', 'name' => 'C N Janaki', 'designation' => 'Assistant Director - Chennai', 'address' => 'Perasiriyar K. Anbazhagan Building, 4th Floor, Nandanam, Chennai - 600035', 'phone' => '9841609986', 'email' => ''],
            ['sl' => '7', 'name' => 'S Parimaladevi', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '9841839554', 'email' => ''],
            ['sl' => '8', 'name' => 'V Sathish Kumar', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '9710258253', 'email' => ''],
            ['sl' => '9', 'name' => 'S Eswari', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '7358802755', 'email' => ''],
            ['sl' => '10', 'name' => 'R Jayanthi', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '9994117853', 'email' => ''],
            ['sl' => '11', 'name' => 'N Velayuthan', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '9952875139', 'email' => ''],
            ['sl' => '12', 'name' => 'K Seran', 'designation' => 'Assistant Director - Chennai', 'address' => '', 'phone' => '9445129767', 'email' => ''],
            ['sl' => '13', 'name' => 'M. Suganya', 'designation' => 'Assistant Director - Dindigul', 'address' => 'District Collectorate Building, Velu Nachiyar Building, 2nd Floor, Dindigul', 'phone' => '9865579200', 'email' => ''],
            ['sl' => '14', 'name' => 'V Arivukannan', 'designation' => 'Assistant Director - Dindigul', 'address' => '', 'phone' => '9443922747', 'email' => ''],
            ['sl' => '15', 'name' => 'A Kumaran', 'designation' => 'Assistant Director - Dindigul', 'address' => '', 'phone' => '7373094040', 'email' => ''],
            ['sl' => '16', 'name' => 'A.S. Narayanan', 'designation' => 'Assistant Director - Madurai', 'address' => '13/A, Kanaga Apartments, 2nd Floor, Lady Doak College Road, Chinna Chokkikulam, Madurai - 625002', 'phone' => '9787238055', 'email' => ''],
            ['sl' => '17', 'name' => 'S Prabakaran', 'designation' => 'Assistant Director - Madurai', 'address' => '', 'phone' => '8807639918', 'email' => ''],
            ['sl' => '18', 'name' => 'C Sultanalaudin', 'designation' => 'Assistant Director - Ramanathapuram', 'address' => 'No. 102, Singaravelar Maligai, Ground Floor, District Collectorate Campus, Ramanathapuram - 623503', 'phone' => '8056521634', 'email' => ''],
            ['sl' => '19', 'name' => 'S. Anandaraj', 'designation' => 'Assistant Director - Ramanathapuram', 'address' => '', 'phone' => '6374159705', 'email' => ''],
            ['sl' => '20', 'name' => 'B Saraswathi', 'designation' => 'Assistant Director - Sivagangai', 'address' => 'Municipal Building, 60 Thondi Road, Sivagangai - 630561', 'phone' => '9943339920', 'email' => ''],
        ];
    }
}
