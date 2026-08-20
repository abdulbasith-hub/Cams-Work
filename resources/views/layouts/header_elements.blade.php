<style>
    .govt_head .g_row1 {
        align-items: center;
    }


    @media(max-width:600px) {
        .govt_head .g_row1 {
            padding: 1%;
        }
        .govt_head  {
            margin-bottom:-6%;
        }

        .g_row1 [class*="col"]:nth-child(odd) {
            width: 28%;
        }

        .g_row1 [class*="col"]:nth-child(even) {
            width: 70%;
        }

        .g_row1 h4,
        .g_row1 h5,
        .g_row1 h6 {
            font-size: 14px;
        }
    }

    .header_content {
        background-color: #fff;
        /* Background color for header */
    }
    #topbar {
        background-color:rgb(0, 63, 165, 0.8);
        font-size: 14px;
        padding: 0;
        color: rgba(255, 255, 255, 1);
        /*  background:   url(../images/bg2.png), linear-gradient(225deg, #ff852b 10%, #003fa5 40% ,#08152e 45%);*/
        /* background: url("{{ asset('site/image/bg2.png') }}"), linear-gradient(225deg, #2bd8ff 10%, #003fa5 30%, #253246 45%); */
        background-size: cover;
        background-position: top;
    }
    @media (max-width: 576px) {
  .dropdown {
    width: auto;
  }
  .dropdown button {
    font-size: 0.85rem;
    padding: 0.3rem 0.6rem;
  }
  .dropdown .fa {
    margin-right: 4px;
  }
  .dropdown-menu .dropdown-item {
    white-space: normal;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
  }
  .image{
    width:70%;
  }
}
.head select {
  min-width: 90px;
}

@media (max-width: 600px) {
  .control-buttons button {
    padding: 2px 6px;
    font-size: 12px;
  }
  
}
.font-btn {
    background-color: transparent;
    color: white;
    border: 1px solid white;
    padding: 4px 10px;
    font-size: 0.9rem;
    border-radius: 3px;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

.font-btn:hover,
.font-btn:focus {
    background-color: white;
    color: black;
    outline: none;
}
.additional {
    font-weight: bold;
    color: blue; /* Set text color */
    text-decoration: none; /* Optional: remove underline */
}

/* Prevent color change on hover */
.additional:hover {
    color: blue; /* Same color as normal */
    text-decoration: none; /* Optional: keep styling consistent */
}

</style>

<script src="{{ asset('site/js/jquery-3.4.1.min.js') }}"></script>

<main class="fullscreen-center">
    <div id="pageWrapper">
        <!-- Header and content -->
        <div class="content-wrapper">
            <div class="phStickyWrap font_div">
                <div class="header_content fixed-top">
                <div id="topbar" class="hdFixerWrap d-lg-block"
                    >
                    <div class="container-fluid py-2">
                        <div class="row align-items-center">
                            <!-- Left side: Government label and Navbar toggle -->
                            <div class="col-6 col-md-6 d-flex align-items-center">
                                <!-- <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                    aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button> -->
                                <a href="screenreader" class="font_div text-white"
                                    style="font-size: 16px; font-family: 'Helvetica', 'Arial', sans-serif;">
                                    <span class="lang" key="screenreaderheading">Screen Reader</span>
                                </a>
                                <div class="ml-2 d-flex justify-content-end control-buttons">
                                    <button class="decrease font-btn mr-2">A-</button>
                                    <button class="resetMe font-btn mr-2">A</button>
                                    <button class="increase font-btn mr-2">A+</button>
                                </div>
                                
                                </div>
                            <!-- Right side: Font size buttons and language dropdown -->
                            <div class="col-6 col-md-6 d-flex  justify-content-end">
                                <select class="custom-select custom-select-sm" id="translate"
                                    style="width: auto; border-color: #0262af;">
                                    <option value="en">English</option>
                                    <option value="ta">தமிழ்</option>
                                </select>
                              
                            </div>
                        </div>
                    </div>
                </div>
                <section class="govt_head g_row1 bg-white mt-0">
                    <div class="container-fluid py-1">
                        <div class="row mt-1 mb-0 align-items-center">
                        
                        <!-- Left Side: Logo and Titles -->
                        <div class="col-10 col-md-10">
                            <div class="row align-items-center image-header">
                            <div class="col-3 col-md-1 d-flex justify-content-center">
                                <img src="{{ asset('site/image/tn__logo.png') }}" class="img-fluid" alt="Logo">
                            </div>
                            <div class="col-9 col-md-11 text-black">
                                <h5 class="nameline3 text-black lang mb-0" key="title1" style="line-height: 1; padding-bottom: 7px; color:black;">
                                Comprehensive Audit Management System
                                </h5>
                                <h5 class="nameline1 lang mb-0" key="dg_office" style="line-height: 1; color:black; padding-bottom: 7px;">
                                Director General of Audit
                                </h5>
                                <h5 class="nameline2 lang mb-0" key="title2" style="line-height: 1; color:black;">
                                Government of Tamil Nadu
                                </h5>
                            </div>
                            </div>
                        </div>

                        <!-- Right Side: Home Button -->
                        <div class="col-2 col-md-2 d-flex justify-content-end">
                            <a href="/" class="lang additional text-primary" key="home" style="font-size: 16px;">Home</a>
                        </div>

                        </div>
                    </div>
                    </section>

                </div>
            </div>
        </div>

    <script>
  $(document).ready(function () {
    const allowedSizes = [10,12,14,16]; // Only these sizes allowed
    const defaultSize = 13;

    function setFontSize(size) {
        $('html').css('font-size', size + 'px');
    }

    function getCurrentFontSize() {
        return parseFloat($('html').css('font-size'));
    }

    $(".resetMe").click(function () {
        setFontSize(defaultSize);
    });

    $(".increase").click(function () {
        const currentSize = getCurrentFontSize();
        const index = allowedSizes.indexOf(currentSize);
        if (index !== -1 && index < allowedSizes.length - 1) {
            setFontSize(allowedSizes[index + 1]);
        }
    });

    $(".decrease").click(function () {
        const currentSize = getCurrentFontSize();
        const index = allowedSizes.indexOf(currentSize);
        if (index > 0) {
            setFontSize(allowedSizes[index - 1]);
        }
    });

    // Optional: Set default size on page load
    setFontSize(defaultSize);
});

function translate() {
        // Retrieve the selected language from local storage or set it to English if not present
        var lang = window.localStorage.getItem('lang');
        if (lang == null)
            lang = 'en';

        // Set a cookie named 'language' with the selected language
        document.cookie = "language=" + lang;

        // Update the value of an element with the id 'translate' to reflect the selected language
        $('#translate').val(lang);


        // Check if an element with the id 'banner_image' exists on the page
        var bannerImageExists = document.getElementById('banner_image') !== null;

        if (bannerImageExists) { // If the element exists, do something

            // Change the source (`src`) attribute of an image with the id 'banner_image' based on the selected language
            // if (lang == "ta") {
            //     document.getElementById('banner_image').src =
            //         "https://10.163.19.176/ctax_3009/public/site/images/image_banner.png";
            // } else {
            //     document.getElementById('banner_image').src =
            //         "https://10.163.19.176/ctax_3009/public/site/images/Brown_Banner.jpg";
            // }
        } else {
            // If the element doesn't exist, do something else
            //console.log("Element with id 'banner_image' does not exist on the page.");
        }

        // Update the text content of elements with the class 'lang' based on the translations stored in the arrLang variable for the selected language
        $('.lang').each(function(index, item) {
            $(this).text(arrLang[lang][$(this).attr('key')]);
        });
    }


    // Process translation
    $(function() {
        $('#translate').change(function() {

            lang = ($(this).val());
            window.localStorage.setItem('lang', ($(this).val()));

            $('#translate').val(window.localStorage.getItem('lang'));

            window.localStorage.setItem('active_menu', '');
            translate();
        });
    });
</script>