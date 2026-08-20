@php
    $sessionchargedel = session('charge', collect([])); // Default to empty collection
    $userMenuIds = array_unique($sessionchargedel->menu ?? []); // Get menu IDs
    $usertypecode = session('usertypecode');
@endphp


<style>
    .custom-select-bg {
        background-color: white !important;
        color: #000;
        appearance: none;
        -moz-appearance: none;
        -webkit-appearance: none;
    }

    .custom-select-bg:focus {
        outline: none;
        box-shadow: none;
        border-color: #86b7fe;
    }

</style>



<input type="hidden" id="usertypecode" name="usertypecode" value="<?php echo $sessionchargedel->usertypecode; ?>">
<?php
// use db;
//Fetch locally stored lang
if (isset($_COOKIE['language'])) {
    $lang_val = $_COOKIE['language'];
    if ($lang_val == '' || $lang_val == null) {
        $lang_val = 'en';
    }
} else {
    $lang_val = 'en';
}
// Fetch child menus
$childMenus = DB::table('audit.mst_menu as m1')->leftJoin('audit.mst_menu as m2', 'm1.parentid', '=', 'm2.menuid')->whereIn('m1.menuid', $userMenuIds)->select('m1.menuid as menuid', 'm1.menuename as menuename', 'm1.menutname as menutname', 'm1.parentid as parentid', 'm1.parentorderid as parentorderid', 'm1.orderid as orderid', 'm1.menuurl as menuurl', 'm1.iconname')->get();

// Group child menus by parentid
$childMenusByParent = $childMenus->groupBy('parentid');

// Get parent menus
$parentIdsQuery = DB::table('audit.mst_menu')->selectRaw('DISTINCT CASE WHEN parentid = 0 THEN menuid ELSE parentid END AS parentid')->whereIn('menuid', $userMenuIds);

$parentMenus = DB::table('audit.mst_menu as m1')
    ->joinSub($parentIdsQuery, 'parentid', function ($join) {
        $join->on('m1.menuid', '=', 'parentid.parentid');
    })
    ->select('m1.menuid', 'm1.menuename', 'm1.menutname', 'm1.parentid', 'm1.parentorderid', 'm1.iconname', 'm1.orderid', 'm1.levelid', 'm1.menuurl')
    ->orderBy('m1.parentorderid')
    ->orderBy('m1.orderid')
    ->get();
?>


<?php
            $userdel    =   session('user');
            $chargedel    =   session('charge');
            $sessionchargeid   =  $chargedel->chargeid; 
            // $charge = DB::table('audit.userchargedetails as uc')
            // ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid') // Adjust the columns as needed
            // ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            // ->select('uc.chargeid','uc.userid','c.chargedescription') // Select all columns from both tables
            // ->where('uc.userid','=',$userdel->userid)
            // ->get();
            if($sessionchargedel->usertypecode == 'A')
            {
                $charge = DB::table('audit.userchargedetails as uc')
    ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid') // Adjust the columns as needed
    ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
    ->select('uc.chargeid','uc.userid','c.chargedescription','uc.chargeflag') // Select all columns from both tables
    ->where('uc.userid','=',$userdel->userid)
    ->where('uc.statusflag' ,'=', 'Y')
    ->where('du.statusflag' ,'=', 'Y')
    ->where('c.statusflag' ,'=', 'Y')
    ->orderBy('c.chargedescription')
    ->get();            }
elseif ($sessionchargedel->usertypecode == 'H') {
    $charge = DB::table('audit.hoduserchargedetails as c')
        ->join('audit.hodchargedetails as au', 'au.chargeid', '=', 'c.chargeid')
        ->select('c.chargeid', 'au.chargedescription',  'c.chargeflag') // Select all columns from both tables
        ->where('c.chargeid', '=', $sessionchargeid)
        ->where('c.userid', '=', $userdel->userid)
        ->where('c.statusflag', '=', 'Y')
        ->where('au.statusflag', '=', 'Y')
        ->orderBy('au.chargedescription')
        ->get();
}
            else
            {
               $charge = DB::table('audit.chargedetails as c')
    ->join('audit.audtieeuserdetails as au', 'au.chargeid', '=', 'c.chargeid')
    ->select('c.chargeid','c.chargedescription', DB::raw("'P' as chargeflag")) // Select all columns from both tables
    ->where('c.chargeid','=',$sessionchargeid)
    ->where('au.auditeeuserid','=',$userdel->userid)
    ->where('c.statusflag','=','Y')
    ->where('au.statusflag','=','Y')
    ->orderBy('c.chargedescription')
    ->get();            }
 if (($charge->isEmpty() ?? true) && !empty($sessionchargedel->chargeid)) {
                $charge = collect([
                    (object) [
                        'chargeid' => $sessionchargedel->chargeid,
                        'chargedescription' => $sessionchargedel->chargedescription ?? 'Current Charge',
                        'chargeflag' => $sessionchargedel->chargeflag ?? 'P',
                    ],
                ]);
            }	

           
            ?>


<aside class="left-sidebar with-vertical" style="background-color: #3782ce;  height: 100%; position: fixed; ">
    {{-- <img src="{{ asset('site/image/tn__logo.png') }}" class="cams_logo ms-2 me-3">
    <b class="text-white h4"> CAMS </b> --}}
    <div>
        <div class="brand-logo d-flex align-items-center ">
            <img src="{{ asset('site/image/tn__logo.png') }}" class="cams_logo ms-2 me-3">
            <b class="text-white h4 hide-menu"> CAMS </b>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <a class="sidebar-brand" style="background-color: #0262af! important; color:white">
                <center>
                <!-- <form action="{{ route('change.charge') }}" method="POST">
                    @csrf
                    <select class="form-select " name="change_charge" id="change_charge" onchange="this.form.submit()" >
                        @foreach ($charge as $value)
                            <option value="{{ $value->chargeid }}" {{ $value->chargeid == $sessionchargeid ? 'selected' : '' }}>
                                {{ $value->chargedescription }}
                            </option>
                        @endforeach
                    </select>
                </form> -->

                <form id="changeChargeForm">
                    @csrf
                    <select class="form-select active custom-select-bg" name="change_charge" id="change_charge" style='background-color:white'>
                        @foreach ($charge as $value)
                            <option value="{{ $value->chargeid }}" {{ $value->chargeid == $sessionchargeid ? 'selected' : '' }}>
                                {{ $value->chargedescription }}
                                {{ $value->chargeflag === 'P' ? ' - Primary' : '' }}

                            </option>
                        @endforeach
                    </select>
                </form>
                </center>
            </a>
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Menu</span>
                </li>

                @foreach ($parentMenus as $parent)
                    @php
                        $parentId = $parent->menuid;
                        if ($lang_val == 'ta') {
                            $parentname = $parent->menutname;
                        } else {
                            $parentname = $parent->menuename;
                        }
                        // $parentname = $parent->menuename;
                        $parentmenuurl = $parent->menuurl;
                    @endphp

                    @if ($parent->levelid == 1)
                        <li class="sidebar-item">
                            <a class="sidebar-link lang" href="{{ url($parentmenuurl) }}" id="get-url"
                                aria-expanded="false" data-menuname-ta="{{ $parent->menutname }}"
                                data-menuname-en="{{ $parent->menuename }}">
                                <span><i class="ti <?php echo $parent->iconname; ?>"></i></span>
                                <span class="hide-menu">{{ $parentname }}</span>
                            </a>
                        </li>
                    @else
                        @php
                            $childMenusForParent = $childMenusByParent->get($parentId, collect())->sortBy('orderid');
                        @endphp

                        @if ($childMenusForParent->isNotEmpty())
                            <li class="sidebar-item">
                                <a class="sidebar-link lang has-arrow" href="javascript:void(0)" aria-expanded="false"
                                    data-menuname-ta="{{ $parent->menutname }}"
                                    data-menuname-en="{{ $parent->menuename }}">
                                    <span class="d-flex"><i class="ti ti-layout-grid"></i></span>
                                    <span class="hide-menu">{{ $parentname }}</span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level" style="margin-left :30px;">
                                    @foreach ($childMenusForParent as $child)
                                        <li class="sidebar-item">
                                            <a href="{{ url($child->menuurl) }}" class="sidebar-link lang"
                                                data-menuname-ta="{{ $child->menutname }}"
                                                data-menuname-en="{{ $child->menuename }}">
                                                <div class="round-16 d-flex align-items-center justify-content-center">
                                                    <i class="ti <?php echo $child->iconname; ?>"></i>
                                                </div>
                                                <span class="hide-menu">{{ $child->menuename }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>



<script>
    document.getElementById('change_charge').addEventListener('change', function () {
        const form = document.getElementById('changeChargeForm');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route('change.charge') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert('Something went wrong!');
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>

