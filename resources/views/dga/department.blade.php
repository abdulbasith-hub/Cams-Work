<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department['short_name'] }} - CAMS</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('site/css/homestyle.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}">
    <style>
			        :root {
			            --dga-ink: #20364a;
			            --dga-blue: #2f75b5;
			            --dga-teal: #48a99a;
			            --dga-gold: #f0b84f;
			            --dga-green: #5fa779;
			            --dga-bg: #f4fbff;
			            --dga-line: #d5e6f1;
			            --dga-muted: #5f7484;
			            --dga-surface: #ffffff;
			            --dga-soft-blue: #eaf6ff;
			            --dga-soft-teal: #edf9f5;
			            --dga-soft-gold: #fff4db;
			            --dga-shadow: rgba(32, 54, 74, .08);
				            --dga-theme-dark: #174f82;
				            --dga-theme-primary: #2f75b5;
				            --dga-theme-primary-dark: #174f82;
				            --dga-theme-heading: #20364a;
				            --dga-theme-secondary: #25756b;
			            --dga-theme-light: #62a9df;
			            --dga-theme-accent: #f0b84f;
			            --dga-theme-soft: #eaf6ff;
			            --dga-theme-soft-2: #edf9f5;
			            --dga-theme-hover: #b7dcf4;
			            --dga-theme-shadow: rgba(47, 117, 181, .24);
			            --dept-accent: var(--dga-blue);
			            --dept-accent-dark: var(--dga-theme-dark);
			            --dept-accent-mid: var(--dga-teal);
				            --dept-accent-soft: var(--dga-soft-blue);
				            --dept-accent-fresh: var(--dga-soft-teal);
				            --dept-accent-ring: rgba(98, 169, 223, .58);
				            --dept-page-bg: var(--dga-bg);
				        }

			        body.dga-theme-green {
			            --dga-ink: #17372f;
			            --dga-blue: #21796c;
			            --dga-teal: #45a98d;
			            --dga-gold: #d9a338;
			            --dga-green: #4e986e;
			            --dga-bg: #f3fbf8;
			            --dga-line: #c8e5dc;
			            --dga-muted: #55756d;
			            --dga-soft-blue: #e7f7f2;
			            --dga-soft-teal: #edf9f5;
			            --dga-soft-gold: #fff5dd;
			            --dga-shadow: rgba(23, 77, 67, .09);
				            --dga-theme-dark: #146a5e;
				            --dga-theme-primary: #21796c;
				            --dga-theme-primary-dark: #146a5e;
				            --dga-theme-heading: #17372f;
				            --dga-theme-secondary: #45a98d;
			            --dga-theme-light: #78c8ae;
			            --dga-theme-accent: #d9a338;
			            --dga-theme-soft: #e7f7f2;
			            --dga-theme-soft-2: #edf9f5;
			            --dga-theme-hover: #bae7d9;
			            --dga-theme-shadow: rgba(33, 121, 108, .24);
			            --dept-accent: var(--dga-blue);
			            --dept-accent-dark: var(--dga-theme-dark);
			            --dept-accent-mid: var(--dga-teal);
			            --dept-accent-soft: var(--dga-soft-blue);
			            --dept-accent-fresh: var(--dga-soft-teal);
				            --dept-accent-ring: rgba(120, 200, 174, .58);
				        }

				        body.dga-theme-gold {
				            --dga-ink: #2f2614;
				            --dga-blue: #9a6a14;
				            --dga-teal: #21796c;
				            --dga-gold: #d9a338;
				            --dga-green: #4e986e;
				            --dga-bg: #fffaf0;
				            --dga-line: #ead9b2;
				            --dga-muted: #706247;
				            --dga-soft-blue: #fff6dc;
				            --dga-soft-teal: #edf9f5;
				            --dga-soft-gold: #fff2cc;
				            --dga-shadow: rgba(112, 86, 29, .1);
				            --dga-theme-dark: #705014;
				            --dga-theme-primary: #a97716;
				            --dga-theme-primary-dark: #705014;
				            --dga-theme-heading: #2f2614;
				            --dga-theme-secondary: #21796c;
				            --dga-theme-light: #f0c05a;
				            --dga-theme-accent: #45a98d;
				            --dga-theme-soft: #fff6dc;
				            --dga-theme-soft-2: #edf9f5;
				            --dga-theme-hover: #f8e6b7;
				            --dga-theme-shadow: rgba(169, 119, 22, .24);
				            --dept-accent: var(--dga-blue);
				            --dept-accent-dark: var(--dga-theme-dark);
				            --dept-accent-mid: var(--dga-teal);
				            --dept-accent-soft: var(--dga-soft-blue);
				            --dept-accent-fresh: var(--dga-soft-teal);
				            --dept-accent-ring: rgba(240, 192, 90, .58);
				        }

					body.dga-department-lfa {
						--dept-accent: #0b5fa5;
						--dept-accent-dark: #084574;
						--dept-accent-mid: #3d8bc2;
							--dept-accent-soft: #e8f4fc;
							--dept-accent-fresh: #edf8fb;
							--dept-accent-ring: rgba(11, 95, 165, .42);
							--dga-bg: #f2f8fd;
							--dept-page-bg: #f2f8fd;
						}

					body.dga-department-sgad {
						--dept-accent: #027c86;
						--dept-accent-dark: #025861;
						--dept-accent-mid: #35a5a1;
							--dept-accent-soft: #e5f7f7;
							--dept-accent-fresh: #edfafa;
							--dept-accent-ring: rgba(2, 124, 134, .42);
							--dga-bg: #f1fbfb;
							--dept-page-bg: #f1fbfb;
						}

					body.dga-department-hria {
						--dept-accent: #8a5b05;
						--dept-accent-dark: #624004;
						--dept-accent-mid: #c18d2f;
							--dept-accent-soft: #fff5dc;
							--dept-accent-fresh: #fff9ed;
							--dept-accent-ring: rgba(138, 91, 5, .42);
							--dga-bg: #fffaf0;
							--dept-page-bg: #fffaf0;
						}

					body.dga-department-dca {
						--dept-accent: #2d7b4f;
						--dept-accent-dark: #1d5636;
						--dept-accent-mid: #65a879;
							--dept-accent-soft: #e8f6ed;
							--dept-accent-fresh: #f0faf3;
							--dept-accent-ring: rgba(45, 123, 79, .42);
							--dga-bg: #f2faf5;
							--dept-page-bg: #f2faf5;
						}

					body.dga-department-milk {
						--dept-accent: #9b2f2f;
						--dept-accent-dark: #6d2020;
						--dept-accent-mid: #c76666;
							--dept-accent-soft: #fceeee;
							--dept-accent-fresh: #fff4f4;
							--dept-accent-ring: rgba(155, 47, 47, .42);
							--dga-bg: #fff5f5;
							--dept-page-bg: #fff5f5;
						}

	        body {
	            margin: 0;
	            background: var(--dga-bg);
	            color: var(--dga-ink);
	            font-family: Arial, Helvetica, sans-serif;
	            overflow-x: hidden;
	        }

	        html {
	            scroll-behavior: smooth;
	        }

		        .dga-topbar {
		            background: linear-gradient(90deg, var(--dga-theme-dark) 0%, var(--dga-theme-primary) 52%, var(--dga-theme-secondary) 100%);
		            color: #fff;
		            font-size: 14px;
		            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .18);
		        }

	        .dga-topbar .container {
	            gap: 12px;
	        }

	        .dga-brand-accessibility,
	        .dga-topbar-tools {
	            display: inline-flex;
	            align-items: center;
	            gap: 10px;
	            flex-wrap: wrap;
	        }

	        .dga-topbar-tools {
	            margin-left: auto;
	        }

	        .dga-screen-reader-link,
	        .dga-screen-reader-link:hover,
	        .dga-screen-reader-link:focus {
	            display: inline-flex;
	            align-items: center;
	            gap: 6px;
	            min-height: 28px;
	            padding: 5px 12px;
	            border-radius: 999px;
	            background: #ffffff;
	            color: var(--dga-theme-dark);
	            font-size: 13px;
	            font-weight: 800;
	            text-decoration: none;
	            box-shadow: 0 6px 14px rgba(5, 31, 55, .16);
	        }

	        .dga-font-controls,
	        .dga-language-switch,
	        .dga-theme-switch {
	            display: inline-flex;
	            align-items: center;
	            gap: 5px;
	            padding: 3px;
	            border: 1px solid rgba(255, 255, 255, .24);
	            border-radius: 999px;
	            background: rgba(255, 255, 255, .12);
	            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .18);
	        }

	        .dga-font-controls button,
	        .dga-language-switch button,
	        .dga-theme-switch button {
	            border: 0;
	            border-radius: 999px;
	            background: transparent;
	            color: rgba(255, 255, 255, .86);
	            font-size: 12px;
	            font-weight: 800;
	            line-height: 1;
	            cursor: pointer;
	            transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
	        }

	        .dga-font-controls button,
	        .dga-language-switch button {
	            min-height: 26px;
	            padding: 6px 10px;
	        }

	        .dga-theme-switch button {
	            width: 26px;
	            height: 26px;
	            padding: 0;
	        }

	        .dga-font-controls button:hover,
	        .dga-font-controls button:focus,
	        .dga-font-controls button.is-active,
	        .dga-language-switch button:hover,
	        .dga-language-switch button:focus,
	        .dga-language-switch button.is-active,
	        .dga-theme-switch button:hover,
	        .dga-theme-switch button:focus,
	        .dga-theme-switch button.is-active {
	            background: #ffffff;
	            color: var(--dga-theme-dark);
	            box-shadow: 0 6px 14px rgba(5, 31, 55, .18);
	            transform: translateY(-1px);
	        }

	        .dga-theme-switch button::before {
	            content: "";
	            display: block;
	            width: 14px;
	            height: 14px;
	            margin: auto;
	            border-radius: 50%;
	            border: 2px solid currentColor;
	        }

	        .dga-theme-switch button[data-dga-theme="blue"]::before {
	            background: linear-gradient(135deg, #174f82 0 48%, #f0b84f 48% 100%);
	        }

		        .dga-theme-switch button[data-dga-theme="green"]::before {
		            background: linear-gradient(135deg, #146a5e 0 48%, #d9a338 48% 100%);
		        }

		        .dga-theme-switch button[data-dga-theme="gold"]::before {
		            background: linear-gradient(135deg, #174f82 0 34%, #d9a338 34% 68%, #45a98d 68% 100%);
		        }

	        .dga-header {
	            position: sticky;
	            top: 0;
	            z-index: 30;
	            background: rgba(255, 255, 255, .98);
	            border-bottom: 1px solid var(--dga-line);
	            box-shadow: 0 8px 24px rgba(19, 42, 69, .08);
	        }

        .dga-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .dga-brand img {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .dga-brand h1 {
	            margin: 0;
		        color: var(--dga-blue);
            font-size: 22px;
            font-weight: 700;
            line-height: 1.1;
        }

        .dga-brand span {
            display: block;
            margin-top: 3px;
		            color: #52687b;
            font-size: 14px;
        }

        .dga-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

	        .dga-nav-link,
	        .dga-nav-link:hover {
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border-radius: 4px;
		            border: 1px solid rgba(98, 169, 223, .42);
	            color: var(--dept-accent-dark);
	            background:
		                linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(234, 246, 255, .78));
	            font-weight: 700;
		            text-decoration: none;
		            box-shadow: 0 8px 18px var(--dga-theme-shadow);
	            transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
		        }

		        .dga-nav-link:hover,
		        .dga-nav-link:focus {
		            background: linear-gradient(135deg, var(--dga-theme-soft), var(--dga-theme-soft-2));
		            color: var(--dga-theme-dark);
		            box-shadow: 0 10px 22px var(--dga-theme-shadow);
		            transform: translateY(-1px);
		        }

	        .dga-nav-link.primary,
	        .dga-nav-link.primary:hover,
	        .dga-nav-link.primary:focus {
	            border-color: var(--dga-theme-primary);
	            background: var(--dga-theme-primary);
	            color: #ffffff;
	            box-shadow: 0 10px 22px var(--dga-theme-shadow);
	        }

		        .dga-department-strip {
		            position: relative;
		            z-index: 20;
			            border-top: 0;
			            background:
			                linear-gradient(90deg, #24689f 0%, #207982 52%, #2c7b6a 100%);
			            box-shadow: 0 9px 22px var(--dga-theme-shadow);
			        }

	        .dga-department-strip::after {
	            content: "";
	            position: absolute;
	            left: 0;
	            right: 0;
	            bottom: 0;
		            height: 2px;
		            background: linear-gradient(90deg, #f0b84f, rgba(255, 255, 255, .74), #9be0d5);
		            opacity: .8;
		        }

	        .dga-department-grid {
	            display: flex;
		            align-items: center;
		            justify-content: flex-start;
			            gap: clamp(14px, 2.3vw, 34px);
			            padding: 10px 0 12px clamp(10px, 2.2vw, 34px);
			            background: transparent;
			        }

	        .dga-department-item {
	            position: relative;
	            flex: 0 0 auto;
	        }

	        .dga-department-title,
	        .dga-department-title:hover,
	        .dga-department-title:focus {
			            min-height: 38px;
		            display: flex;
		            align-items: center;
		            justify-content: center;
		            gap: 8px;
			            padding: 8px 4px;
			            border: 0;
			            border-radius: 0;
			            background: transparent;
			            color: rgba(255, 255, 255, .9);
			            text-decoration: none;
			            box-shadow: none;
			            transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
			        }

	        .dga-department-title:hover,
	        .dga-department-title:focus,
	        .dga-department-title.is-active {
			            border-color: transparent;
			            background: transparent;
			            color: #ffffff;
			            transform: translateY(-1px);
			        }

			        .dga-department-title.is-active {
			            box-shadow: none;
			        }

		        .dga-department-title:focus {
			            outline: 2px solid rgba(240, 184, 79, .72);
		            outline-offset: 2px;
		        }

	        .dga-department-title strong {
	            color: inherit;
		            font-size: 14px;
		            font-weight: 800;
		            letter-spacing: .04em;
		            line-height: 1;
		        }

		        .dga-department-title::after {
		            content: "";
		            position: absolute;
		            left: 0;
		            right: 0;
		            bottom: -8px;
		            height: 3px;
		            border-radius: 999px;
		            background: linear-gradient(90deg, #fff4c7, #9be0d5);
		            opacity: 0;
		            transform: scaleX(.35);
		            transform-origin: center;
		            transition: opacity .22s ease, transform .22s ease;
		        }

		        .dga-department-title:hover::after,
		        .dga-department-title:focus::after,
		        .dga-department-title.is-active::after {
		            opacity: 1;
		            transform: scaleX(1);
		        }

		        .dga-department-arrow {
		            display: inline-flex;
		            width: 18px;
		            height: 18px;
		            align-items: center;
		            justify-content: center;
		            border-radius: 50%;
		            background: rgba(255, 255, 255, .12);
		            color: rgba(255, 255, 255, .9);
		            font-size: 11px;
		            transition: transform .22s ease, background .22s ease, color .22s ease;
		        }

		        .dga-department-title:hover .dga-department-arrow,
		        .dga-department-title:focus .dga-department-arrow,
		        .dga-department-title.is-active .dga-department-arrow,
		        .dga-department-item:hover .dga-department-arrow,
		        .dga-department-item:focus-within .dga-department-arrow {
		            /* background: #fff4c7; */
		            color: #174f82;
		            transform: rotate(180deg);
		        }

	        .dga-department-menu {
	            position: absolute;
	            left: 0;
	            right: auto;
	            top: 100%;
	            display: none;
	            min-width: 230px;
	            padding: 7px;
			            border: 1px solid rgba(98, 169, 223, .42);
		            border-radius: 0 0 8px 8px;
		            background:
			                linear-gradient(135deg, rgba(234, 246, 255, .98), rgba(255, 255, 255, .96) 54%, rgba(237, 249, 245, .94));
			            box-shadow: 0 18px 42px var(--dga-theme-shadow);
		            z-index: 45;
		        }

	        .dga-department-item:hover .dga-department-menu,
	        .dga-department-item:focus-within .dga-department-menu {
	            display: block;
	        }

	        .dga-department-menu a {
	            display: block;
	            padding: 9px 10px;
	            border-radius: 4px;
			            color: var(--dga-theme-dark);
		            font-size: 13px;
		            font-weight: 700;
		            text-decoration: none;
		            background: rgba(255, 255, 255, .58);
		        }

		        .dga-department-menu a:hover,
		        .dga-department-menu a:focus {
			            background: linear-gradient(90deg, var(--dga-theme-soft), var(--dga-theme-soft-2));
			            color: var(--dga-theme-dark);
		            text-decoration: none;
		        }

		        .dga-department-body {
		            background: var(--dga-bg);
		        }

		        .dept-page.dga-page-sections-band {
		            min-height: calc(100svh - var(--dga-fixed-top-height, 164px));
		            padding: clamp(12px, 2vh, 20px) 0 42px;
				            background:
				                var(--dept-page-bg);
			        }

		        .dga-page-sections-inner {
		            display: flex;
		            flex-direction: column;
		            gap: 22px;
		        }

		        .dept-kicker {
		            display: inline-flex;
		            align-items: center;
		            min-height: 34px;
		            padding: 6px 14px;
		            border-radius: 999px;
		            border: 1px solid rgba(255, 255, 255, .42);
		            background: rgba(255, 255, 255, .14);
		            color: #fff;
		            font-weight: 800;
		            letter-spacing: 0;
		        }

	        .dept-intro {
	            position: relative;
	            display: grid;
	            grid-template-columns: minmax(0, 1fr) 340px;
	            gap: 24px;
	            align-items: stretch;
	            margin-bottom: 30px;
		            padding: 0;
		            border: 1px solid rgba(216, 226, 239, .95);
		            border-radius: 8px;
		            background:
		                linear-gradient(135deg, rgba(11, 95, 165, .06), rgba(255, 255, 255, .95) 42%),
		                #fff;
		            box-shadow: 0 18px 42px rgba(22, 32, 42, .09);
		            min-width: 0;
		            overflow: hidden;
		        }

		        .dept-intro::before {
		            content: "";
		            position: absolute;
		            left: 0;
		            top: 0;
		            bottom: 0;
		            width: 8px;
			            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
		            opacity: .95;
		        }

	        .dept-title-panel,
	        .dept-fact-strip,
	        .dept-side,
	        .dept-block,
	        .dept-stat,
	        .dept-region {
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background: #fff;
        }

		        .dept-title-panel {
		            position: relative;
		            padding: 30px 34px 32px 38px;
		            border: 0;
			            background:
				                linear-gradient(135deg, #063b67, var(--dga-blue) 58%, var(--dga-teal));
		            color: #fff;
		            box-shadow: none;
		            min-width: 0;
		            overflow: hidden;
		        }

		        .dept-title-panel::before {
		            content: "Office Profile";
		            position: absolute;
		            right: 28px;
		            top: 24px;
		            width: auto;
		            height: auto;
		            padding: 8px 12px;
		            border: 1px solid rgba(255, 255, 255, .28);
		            border-radius: 999px;
		            transform: none;
			            background: rgba(255, 255, 255, .14);
		            color: rgba(255, 255, 255, .9);
		            font-size: 13px;
		            font-weight: 800;
		        }

		        .dept-title-panel::after {
		            content: "";
		            position: absolute;
		            right: -70px;
		            bottom: -90px;
		            width: 220px;
		            height: 220px;
		            border-radius: 50%;
		            border: 34px solid rgba(255, 255, 255, .08);
		            background: transparent;
		        }

		        .dept-title-panel h2 {
		            position: relative;
		            max-width: 760px;
		            margin: 18px 0 16px;
		            color: #fff;
		            font-size: 42px;
		            font-weight: 800;
		            line-height: 1.12;
		        }

		        .dept-title-panel p {
		            position: relative;
		            max-width: 980px;
		            margin: 0;
		            padding: 16px 18px;
			            border-left: 4px solid var(--dga-theme-accent);
		            border-radius: 8px;
		            background: rgba(255, 255, 255, .12);
		            color: rgba(255, 255, 255, .92);
		            font-size: 17px;
		            line-height: 1.7;
		        }

		        .dept-fact-strip {
		            display: grid;
		            gap: 12px;
		            align-content: center;
		            padding: 24px 24px 24px 0;
		            border: 0;
		            background: transparent;
		        }

	        .dept-fact {
	            position: relative;
	            padding: 18px;
	            border: 1px solid rgba(216, 226, 239, .9);
		            border-left: 5px solid var(--dga-blue);
	            border-radius: 8px;
		            background: rgba(255, 255, 255, .92);
	            color: var(--dga-ink);
		            box-shadow: 0 12px 28px var(--dga-shadow);
	        }

	        .dept-fact::before {
	            content: "";
	            position: absolute;
	            left: -12px;
	            top: 22px;
	            width: 14px;
	            height: 14px;
	            border-radius: 50%;
		            background: var(--dga-blue);
	            box-shadow: 0 0 0 4px rgba(255, 255, 255, .9);
	        }

	        .dept-fact:last-child {
	            border-bottom: 1px solid rgba(216, 226, 239, .9);
	        }

	        .dept-fact strong {
	            display: block;
		            color: var(--dga-blue);
	            font-size: 26px;
	            margin-bottom: 5px;
	        }

		        .dept-fact span {
			            color: var(--dga-muted);
		        }

        .lfa-visual-strip {
            align-content: stretch;
        }

        .lfa-visual-card {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            padding: 0;
            border: 1px solid rgba(216, 226, 239, .95);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(22, 32, 42, .09);
            overflow: hidden;
        }

        .lfa-visual-card::before {
            content: none;
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--dept-accent);
        }

        .lfa-visual-media {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 360px;
            border-radius: 8px;
	            background: var(--dga-soft-blue);
            border: 0;
            text-decoration: none;
            overflow: hidden;
        }

        .lfa-visual-media:hover,
        .lfa-visual-media:focus {
	            border-color: var(--dga-blue);
	            box-shadow: 0 12px 26px var(--dga-shadow);
            outline: none;
        }

        .lfa-visual-media img {
            width: 100%;
            height: 100%;
            min-height: 360px;
            max-height: none;
            object-fit: cover;
        }

        .lfa-visual-media.is-photo img {
            width: 100%;
            height: 100%;
        }

			        .dept-layout.dga-hero-section-grid {
		            display: grid;
		            grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
		            gap: clamp(14px, 2vw, 22px);
		            align-items: start;
		            min-width: 0;
		            max-width: 100%;
		        }

				        .dept-side.dga-hero-menu-card {
				            position: sticky;
				            top: var(--dga-sticky-nav-top, 12px);
			            display: flex;
			            flex-direction: column;
			            gap: 16px;
			            padding: 24px 18px 20px 24px;
			            border: 1px solid rgba(255, 255, 255, .28);
			            border-top: 0;
			            border-radius: 8px;
			            background:
			                linear-gradient(180deg, rgba(248, 251, 255, .99), rgba(231, 239, 249, .97));
			            box-shadow: 20px 30px 64px rgba(28, 57, 99, .16);
			            overflow: hidden;
			        }

			        .dept-side.dga-hero-menu-card::before {
			            content: "";
			            position: absolute;
			            left: 0;
			            top: 0;
		            bottom: 0;
		            width: 10px;
		            border-radius: 8px 0 0 8px;
			            background: var(--dga-blue);
			            box-shadow: 8px 0 22px var(--dga-theme-shadow);
			        }

			        .dept-side.dga-hero-menu-card::after {
			            content: "";
			            position: absolute;
			            right: -96px;
			            top: -92px;
			            width: 210px;
			            height: 210px;
			            border-radius: 50%;
			            background: rgba(200, 218, 242, .76);
			            box-shadow: -38px 52px 0 rgba(255, 235, 189, .7);
			            pointer-events: none;
			        }

		        .dept-side .dga-hero-menu-title {
		            position: relative;
		            z-index: 1;
		            align-self: center;
		            margin: 6px 0 12px;
		            padding: 0 18px 14px;
		            color: var(--dga-ink);
		            font-size: 27px;
		            font-weight: 900;
		            line-height: 1.05;
		            text-align: center;
		        }

		        .dept-side .dga-hero-menu-title::after {
		            content: "";
		            position: absolute;
		            left: 50%;
		            bottom: 0;
		            width: 172px;
		            height: 4px;
		            border-radius: 999px;
		            background: var(--dga-blue);
		            transform: translateX(-50%);
		        }

		        .dept-side .dga-hero-menu {
		            position: relative;
		            z-index: 1;
		            display: grid;
		            gap: 11px;
		            padding: 8px 2px 0;
		        }

				        .dept-side .dga-hero-menu-button,
				        .dept-side .dga-hero-menu-button:hover,
				        .dept-side .dga-hero-menu-button:focus {
			            position: relative;
			            display: flex;
			            align-items: center;
			            gap: 12px;
			            min-height: 52px;
			            padding: 10px 16px 10px 15px;
			            overflow: hidden;
				            border: 1px solid var(--dga-menu-border, #c8e0ef);
			            border-radius: 8px;
				            background: #ffffff;
				            color: var(--dga-menu-text, #174f82);
				            font-weight: 800;
				            text-decoration: none;
				            --dga-menu-dark: #2f75b5;
				            --dga-menu-light: #62a9df;
				            --dga-menu-accent: #2f75b5;
				            --dga-menu-active-gradient: linear-gradient(135deg, #2f75b5, #62a9df);
				            --dga-menu-hover-gradient: linear-gradient(135deg, #edf7ff, #b7dcf4);
				            --dga-menu-text: #174f82;
				            --dga-menu-border: #c8e0ef;
				            --dga-menu-icon-bg: rgba(47, 117, 181, .1);
				            --dga-menu-shadow: rgba(47, 117, 181, .24);
				            box-shadow: 0 10px 22px rgba(28, 57, 99, .08);
					            transition: transform .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease, color .2s ease;
					        }

					        .dept-side .dga-hero-menu-button:nth-child(n) {
				            --dga-menu-dark: #2f75b5;
				            --dga-menu-light: #62a9df;
				            --dga-menu-accent: #2f75b5;
				            --dga-menu-active-gradient: linear-gradient(135deg, #2f75b5, #62a9df);
				            --dga-menu-hover-gradient: linear-gradient(135deg, #edf7ff, #b7dcf4);
				            --dga-menu-text: #174f82;
				            --dga-menu-border: #c8e0ef;
				            --dga-menu-icon-bg: rgba(47, 117, 181, .1);
				            --dga-menu-shadow: rgba(47, 117, 181, .24);
				        }

		        .dept-side .dga-hero-menu-button::after {
		            content: "";
		            position: absolute;
		            left: 0;
		            top: 0;
		            bottom: 0;
		            width: 7px;
		            background: var(--dga-menu-hover-gradient);
		            z-index: 0;
		            transition: width .28s ease;
		        }

		        .dept-side .dga-hero-menu-button i {
		            position: relative;
		            z-index: 1;
		            display: inline-flex;
		            align-items: center;
		            justify-content: center;
		            flex: 0 0 34px;
		            width: 34px;
		            height: 34px;
		            border-radius: 8px;
		            background: var(--dga-menu-icon-bg, var(--dga-soft-blue));
		            color: inherit;
		        }

		        .dept-side .dga-hero-menu-button span {
		            position: relative;
		            z-index: 1;
		        }

	        .dept-side .dga-hero-menu-button:hover,
	        .dept-side .dga-hero-menu-button:focus {
		            transform: translateX(3px) scale(1.035);
		            border-color: #3c8ccc;
		            color: var(--dga-menu-text);
		            box-shadow: 0 16px 30px var(--dga-menu-shadow, var(--dga-theme-shadow));
		            outline: none;
		        }

		        .dept-side .dga-hero-menu-button.is-active {
		            transform: translateX(3px) scale(1.025);
		            border-color: var(--dga-menu-accent);
		            color: #fff;
		            box-shadow: 0 16px 30px var(--dga-menu-shadow, var(--dga-theme-shadow));
		        }

		        .dept-side .dga-hero-menu-button:hover::after,
		        .dept-side .dga-hero-menu-button:focus::after {
		            width: 100%;
		            background: var(--dga-menu-hover-gradient);
		        }

		        .dept-side .dga-hero-menu-button.is-active::after {
		            width: 100%;
		            background: var(--dga-menu-active-gradient);
		        }

		        .dept-side .dga-hero-menu-button:hover i,
		        .dept-side .dga-hero-menu-button:focus i {
		            background: rgba(47, 117, 181, .18);
		            color: var(--dga-menu-text);
		        }

		        .dept-side .dga-hero-menu-button.is-active i {
		            background: rgba(255, 255, 255, .22);
		            color: #fff;
		        }

		        .dept-content.dga-hero-content-shell {
	            display: grid;
	            gap: 20px;
	            padding: 20px;
	            border: 1px solid rgba(255, 255, 255, .22);
	            border-radius: 8px;
	            background: rgba(255, 255, 255, .94);
	            box-shadow: 0 24px 60px rgba(0, 0, 0, .22);
	            min-width: 0;
	            max-width: 100%;
	            overflow: visible;
	        }

        .dept-two-col {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            min-width: 0;
        }

			        .dept-block {
			            --dept-section-accent: var(--dga-blue);
			            --dept-section-soft: var(--dga-soft-blue);
			            padding: 24px;
			            border-top: 0;
			            border-left: 6px solid var(--dept-section-accent);
			            background:
			                linear-gradient(135deg, rgba(255, 255, 255, .98), var(--dept-section-soft));
			            box-shadow: 0 18px 42px var(--dga-shadow);
			            min-width: 0;
			            max-width: 100%;
			            overflow: hidden;
		            scroll-margin-top: 150px;
		        }

	        .dept-block h3 {
            margin: 0 0 16px;
            color: var(--dga-ink);
            font-size: 24px;
            font-weight: 800;
        }

        .dept-block h3 span {
            display: block;
            margin-bottom: 5px;
	            color: var(--dept-section-accent);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .dga-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dga-list li {
            position: relative;
            margin-bottom: 12px;
            padding-left: 28px;
            line-height: 1.65;
        }

	        .dga-list li::before {
	            content: "";
	            position: absolute;
	            left: 0;
	            top: 10px;
            width: 10px;
            height: 10px;
	            border-radius: 50%;
		            background: var(--dept-section-accent);
	        }

	        .mindmap {
	            display: flex;
	            gap: 12px;
	            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 8px;
        }

	        .mindmap-node {
	            position: relative;
	            flex: 0 0 210px;
	            min-height: 136px;
	            padding: 18px;
	            border-radius: 8px;
	            background:
	                linear-gradient(180deg, #fff, #f8fbff);
	            border: 1px solid var(--dga-line);
	            box-shadow: 0 10px 24px rgba(22, 32, 42, .06);
	        }

        .mindmap-node::after {
            content: "";
            position: absolute;
            top: 50%;
            right: -13px;
            width: 13px;
            height: 2px;
            background: var(--dept-accent);
        }

        .mindmap-node:last-child::after {
            display: none;
        }

        .mindmap-node strong {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin-bottom: 10px;
            border-radius: 50%;
            background: var(--dept-accent);
            color: #fff;
        }

        .org-map {
            padding: 22px 16px 28px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid rgba(22, 32, 42, .16);
            overflow-x: auto;
        }

        .org-map-inner {
            position: relative;
            min-width: 760px;
            max-width: 900px;
            margin: 0 auto;
            padding: 2px 0 18px;
        }

        .org-level {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .org-level + .org-level {
            margin-top: 28px;
        }

        .org-node {
            position: relative;
            width: 270px;
            min-height: 42px;
            padding: 9px 12px;
            border: 2px solid #9b9b9b;
            border-radius: 2px;
            background: #fff;
            color: #151515;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.25;
            box-shadow: 0 8px 18px rgba(22, 32, 42, .05);
            cursor: default;
            transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
            z-index: 2;
        }

        .org-node:hover,
        .org-node:focus,
        .org-node:focus-within {
            transform: translateY(-3px);
            border-color: var(--dept-accent);
            box-shadow: 0 18px 34px rgba(22, 32, 42, .14);
            outline: none;
            z-index: 80;
        }

        .org-node.org-root {
            width: 660px;
            min-height: 38px;
            font-size: 15px;
        }

        .org-node.org-lead {
            width: 250px;
        }

        .org-node-title {
            display: block;
        }

        .org-popup {
            position: absolute;
            left: 50%;
            bottom: calc(100% + 12px);
            width: min(820px, 84vw);
            max-height: none;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid rgba(216, 226, 239, .72);
            background: #092a43;
            color: #fff;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
            box-shadow: 0 22px 42px rgba(22, 32, 42, .28);
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, 8px);
            transition: opacity .16s ease, transform .16s ease, visibility .16s ease;
            pointer-events: auto;
            overflow: visible;
            z-index: 30;
            user-select: text;
        }

        .org-node.popup-down .org-popup {
            top: calc(100% + 12px);
            bottom: auto;
            transform: translate(-50%, -8px);
        }

        .org-stream.is-left .org-popup {
            left: calc(100% + 16px);
            right: auto;
            top: 50%;
            bottom: auto;
            transform: translate(8px, -50%);
        }

        .org-stream.is-right .org-popup {
            left: auto;
            right: calc(100% + 16px);
            top: 50%;
            bottom: auto;
            transform: translate(-8px, -50%);
        }

        .org-node:hover .org-popup,
        .org-node:focus .org-popup,
        .org-node:focus-within .org-popup {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, 0);
        }

        .org-stream.is-left .org-node:hover .org-popup,
        .org-stream.is-left .org-node:focus .org-popup,
        .org-stream.is-left .org-node:focus-within .org-popup {
            transform: translate(0, -50%);
        }

        .org-stream.is-right .org-node:hover .org-popup,
        .org-stream.is-right .org-node:focus .org-popup,
        .org-stream.is-right .org-node:focus-within .org-popup {
            transform: translate(0, -50%);
        }

        .org-popup strong {
            display: block;
            margin-bottom: 10px;
            color: #fff;
            font-size: 14px;
        }

        .org-popup p {
            margin: 0;
            color: rgba(255, 255, 255, .9);
        }

        .org-table-scroll {
            max-height: 380px;
            overflow: auto;
            border-radius: 6px;
            background: #fff;
        }

        .org-popup table {
            width: 100%;
            min-width: 760px;
            margin: 0;
            border-collapse: collapse;
            background: #fff;
            color: #182334;
            font-weight: 600;
        }

        .org-popup th,
        .org-popup td {
            padding: 8px 9px;
            border: 1px solid #d8e2ef;
            vertical-align: top;
            text-align: left;
        }

        .org-popup th {
            position: sticky;
            top: 0;
            background: #edf5ff;
            color: #092a43;
            z-index: 1;
        }

        .org-popup td:first-child,
        .org-popup th:first-child {
            width: 46px;
            text-align: center;
        }

        .org-link {
            position: relative;
            width: 1px;
            height: 28px;
            margin: 0 auto;
            background: #4a4a4a;
        }

        .org-link::after {
            content: "";
            position: absolute;
            left: -4px;
            bottom: 0;
            width: 8px;
            height: 8px;
            border-right: 1px solid #4a4a4a;
            border-bottom: 1px solid #4a4a4a;
            transform: rotate(45deg);
        }

        .org-split {
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 150px;
            margin-top: 46px;
            padding-top: 20px;
        }

        .org-split::before {
            content: "";
            position: absolute;
            top: 0;
            left: 22%;
            right: 22%;
            height: 1px;
            background: #4a4a4a;
        }

        .org-stream {
            position: relative;
            display: grid;
            justify-items: center;
        }

        .org-stream::before {
            content: "";
            position: absolute;
            top: -20px;
            left: 50%;
            width: 1px;
            height: 20px;
            background: #4a4a4a;
        }

        .org-common {
            position: relative;
            display: grid;
            justify-items: center;
            margin-top: 32px;
            padding-top: 22px;
        }

        .org-common::before {
            content: "";
            position: absolute;
            top: 0;
            left: 31%;
            right: 31%;
            height: 1px;
            background: #4a4a4a;
        }

        .org-common .org-node:first-child .org-side-arrow {
            position: absolute;
            top: -23px;
            width: 1px;
            height: 23px;
            background: #4a4a4a;
        }

        .org-common .org-node:first-child .org-side-arrow.left {
            left: -125px;
        }

	        .org-common .org-node:first-child .org-side-arrow.right {
	            right: -125px;
	        }

	        .org-map.is-lfa-flow {
	            --lfa-flow-line: color-mix(in srgb, var(--dga-theme-primary) 48%, #ffffff);
	            padding: clamp(22px, 2.8vw, 36px);
	            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
	            border-radius: 8px;
	            background:
	                linear-gradient(135deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-soft) 66%, #ffffff));
	            box-shadow: 0 22px 54px rgba(22, 32, 42, .1);
	        }

	        .org-map.is-lfa-flow .org-map-inner {
		            --lfa-node-width: 300px;
		            --lfa-split-gap: 140px;
		            min-width: 920px;
		            max-width: 1040px;
		            padding: 4px 0 18px;
		        }

	        .org-map.is-lfa-flow .org-node {
		            width: var(--lfa-node-width);
	            min-height: 54px;
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            padding: 12px 16px;
	            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
	            border-radius: 8px;
	            background:
	                linear-gradient(180deg, #ffffff, color-mix(in srgb, var(--dga-theme-soft) 64%, #ffffff));
	            color: var(--dga-theme-dark);
	            font-weight: 800;
	            box-shadow: 0 12px 26px rgba(22, 32, 42, .08);
	        }

	        .org-map.is-lfa-flow .org-node.org-root {
	            width: min(100%, 540px);
	            min-height: 78px;
	            border-color: color-mix(in srgb, var(--dga-theme-accent) 42%, #ffffff);
	            background:
	                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-accent) 28%, #ffffff), #ffffff);
	            color: var(--dga-theme-dark);
	            font-size: 22px;
	            box-shadow: 0 18px 38px color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
	        }

	        .org-map.is-lfa-flow .org-node.org-lead {
	            width: 380px;
	            border: 0;
	            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
	            color: #ffffff;
	            box-shadow: 0 14px 28px var(--dga-theme-shadow);
	        }

	        .org-map.is-lfa-flow .org-link,
	        .org-map.is-lfa-flow .org-split::before,
	        .org-map.is-lfa-flow .org-stream::before,
	        .org-map.is-lfa-flow .org-common::before,
	        .org-map.is-lfa-flow .org-common .org-node:first-child .org-side-arrow {
	            background: var(--lfa-flow-line);
	        }

	        .org-map.is-lfa-flow .org-link {
	            width: 2px;
	            height: 30px;
	        }

	        .org-map.is-lfa-flow .org-link::after {
	            left: -4px;
	            width: 10px;
	            height: 10px;
	            border-right: 2px solid var(--lfa-flow-line);
	            border-bottom: 2px solid var(--lfa-flow-line);
	        }

	        .org-map.is-lfa-flow .org-split {
		            column-gap: var(--lfa-split-gap);
		            margin-top: 42px;
		            padding-top: 24px;
		        }

		        .org-map.is-lfa-flow .org-split::before,
		        .org-map.is-lfa-flow .org-common::before {
		            left: calc((100% - var(--lfa-split-gap)) / 4);
		            right: calc((100% - var(--lfa-split-gap)) / 4);
		        }

	        .org-map.is-lfa-flow .org-split::before,
	        .org-map.is-lfa-flow .org-common::before {
		            height: 2px;
		        }

	        .org-map.is-lfa-flow .org-stream::before {
	            top: -24px;
	            width: 2px;
	            height: 24px;
	        }

		        .org-map.is-lfa-flow .org-stream > .org-link:last-child {
		            display: block;
		            height: 34px;
		        }

		        .org-map.is-lfa-flow .org-stream > .org-link:last-child::after {
		            content: none;
		        }

	        .org-map.is-lfa-flow .org-common {
		            margin-top: 0;
		            padding-top: 26px;
		        }

		        .org-map.is-lfa-flow .org-common::after {
		            content: "";
		            position: absolute;
		            top: 0;
		            left: 50%;
		            width: 2px;
		            height: 26px;
		            background: var(--lfa-flow-line);
		            transform: translateX(-50%);
		        }

		        .org-map.is-lfa-flow .org-common .org-node:first-child .org-side-arrow {
		            display: none;
		        }

	        .org-map.is-lfa-flow .org-node:hover,
	        .org-map.is-lfa-flow .org-node:focus,
	        .org-map.is-lfa-flow .org-node:focus-within {
	            border-color: var(--dga-theme-primary);
	            box-shadow: 0 18px 34px var(--dga-theme-shadow);
	        }

	        .document-map {
            padding: 18px;
            border-radius: 8px;
            border: 1px solid rgba(216, 226, 239, .95);
            background: #fff;
            box-shadow: 0 12px 28px rgba(22, 32, 42, .06);
            overflow-x: auto;
        }

        .document-map img {
            display: block;
            width: 100%;
            min-width: 760px;
            max-width: 980px;
            height: auto;
            margin: 0 auto;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            min-width: 0;
        }

			        .dept-stat {
			            position: relative;
			            min-height: 118px;
			            padding: 18px;
			            border: 1px solid rgba(216, 226, 239, .95);
			            border-top: 4px solid var(--dept-section-accent, var(--dga-blue));
			            background:
			                linear-gradient(180deg, #fff, var(--dept-section-soft, var(--dga-soft-blue)));
			            box-shadow: 0 12px 26px var(--dga-shadow);
			            overflow: hidden;
			        }

        .dept-stat::before {
            content: "";
            position: absolute;
            right: -24px;
            top: -24px;
            width: 78px;
            height: 78px;
            border-radius: 50%;
	            background: color-mix(in srgb, var(--dept-section-accent, var(--dga-blue)) 12%, transparent);
        }

        .dept-stat strong {
            position: relative;
            display: block;
	            color: var(--dept-section-accent, var(--dga-blue));
            font-size: 30px;
            margin-bottom: 8px;
            line-height: 1;
        }

        .dept-stat span {
            position: relative;
            display: block;
	            color: var(--dga-ink);
            font-weight: 700;
            line-height: 1.35;
        }

        .institution-panel {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
            min-width: 0;
        }

        .institution-total {
            position: relative;
            min-height: 100%;
            padding: 24px;
            border-radius: 8px;
	            background: linear-gradient(135deg, #063b67, var(--dga-blue) 58%, var(--dga-teal));
            color: #fff;
            overflow: hidden;
            box-shadow: 0 18px 36px rgba(22, 32, 42, .14);
        }

        .institution-total::after {
            content: "";
            position: absolute;
            right: -52px;
            bottom: -52px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 28px solid rgba(255, 255, 255, .1);
        }

        .institution-total span {
            position: relative;
            display: block;
            margin-bottom: 14px;
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .institution-total strong {
            position: relative;
            display: block;
            margin-bottom: 12px;
            color: #fff;
            font-size: 54px;
            line-height: 1;
        }

        .institution-total p {
            position: relative;
            margin: 0;
            color: rgba(255, 255, 255, .92);
            font-weight: 700;
            line-height: 1.5;
        }

        .region-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            min-width: 0;
        }

        .dept-region {
            padding: 16px;
        }

        .dept-region h4 {
            margin-bottom: 12px;
	            color: var(--dept-section-accent, var(--dga-blue));
            font-size: 18px;
            font-weight: 800;
        }

        .dept-region p {
            line-height: 1.7;
        }

		        .dga-footer {
		            position: relative;
		            padding: 20px 0 18px;
		            background:
		                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-dark) 90%, #071e31), var(--dga-theme-primary));
		            color: #fff;
		            overflow: hidden;
		        }

		        .dga-footer .container {
		            position: relative;
		        }

		        .dga-home-footer-strip {
		            position: relative;
		            display: grid;
		            grid-template-columns: minmax(300px, .9fr) minmax(360px, 1.1fr);
		            gap: 14px;
		            margin: 0 0 12px;
		            padding: 20px 84px 20px 20px;
		            border: 1px solid color-mix(in srgb, var(--dga-theme-light) 22%, rgba(255, 255, 255, .18));
		            border-radius: 8px;
		            background:
		                linear-gradient(120deg, color-mix(in srgb, var(--dga-theme-dark) 92%, #071e31) 0%, var(--dga-theme-primary) 48%, color-mix(in srgb, var(--dga-theme-secondary) 84%, #102f4a) 100%);
		            box-shadow: 0 20px 44px color-mix(in srgb, var(--dga-theme-shadow) 70%, rgba(7, 30, 49, .22));
		            overflow: hidden;
		        }

		        .dga-home-footer-strip::before {
		            content: "";
		            position: absolute;
		            top: 0;
		            left: 0;
		            right: 0;
		            height: 5px;
		            background:
		                linear-gradient(90deg, var(--dga-theme-accent) 0 22%, #ffffff 22% 42%, var(--dga-theme-light) 42% 72%, var(--dga-theme-accent) 72% 100%);
		        }

		        .dga-home-footer-strip::after {
		            content: "";
		            position: absolute;
		            inset: 5px 0 auto;
		            height: 1px;
		            background: rgba(255, 255, 255, .18);
		            pointer-events: none;
		        }

		        .dga-home-footer-center,
		        .dga-home-footer-meta {
		            position: relative;
		            z-index: 1;
		            min-width: 0;
		            min-height: 118px;
		            padding: 18px 20px;
		            border: 1px solid rgba(255, 255, 255, .22);
		            border-left: 5px solid var(--dga-theme-light);
		            border-radius: 8px;
		            background:
		                linear-gradient(180deg, rgba(255, 255, 255, .97), color-mix(in srgb, var(--dga-theme-soft) 64%, #ffffff));
		            box-shadow: 0 14px 30px rgba(13, 45, 73, .14);
		        }

		        .dga-home-footer-meta {
		            border-left-color: var(--dga-theme-accent);
		        }

		        .dga-home-footer-heading {
		            display: inline-flex;
		            align-items: center;
		            gap: 8px;
		            margin: 0 0 12px;
		            color: var(--dga-theme-dark);
		            font-size: 12px;
		            font-weight: 900;
		            letter-spacing: .02em;
		            text-transform: uppercase;
		        }

		        .dga-home-footer-heading i,
		        .dga-home-footer-mail i {
		            display: inline-flex;
		            align-items: center;
		            justify-content: center;
		            width: 32px;
		            height: 32px;
		            border-radius: 8px;
		            background: color-mix(in srgb, var(--dga-theme-primary) 13%, #ffffff);
		            color: var(--dga-theme-primary);
		        }

		        .dga-home-footer-mail,
		        .dga-footer-support-list li {
		            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
		            border-radius: 8px;
		            background: rgba(255, 255, 255, .86);
		            color: var(--dga-theme-dark);
		        }

		        .dga-home-footer-mail {
		            display: inline-flex;
		            align-items: center;
		            gap: 8px;
		            padding: 8px 10px;
		            font-size: 14px;
		            font-weight: 800;
		            text-decoration: none;
		        }

		        .dga-home-footer-note {
		            margin: 10px 0 0;
		            color: var(--dga-muted);
		            font-size: 13px;
		            line-height: 1.55;
		        }

		        .dga-footer-support-list {
		            display: grid;
		            gap: 8px;
		            margin: 0;
		            padding: 0;
		            list-style: none;
		        }

		        .dga-footer-support-list li {
		            position: relative;
		            padding: 9px 12px 9px 30px;
		            font-size: 13px;
		            font-weight: 700;
		            line-height: 1.45;
		        }

		        .dga-footer-support-list li::before {
		            content: "";
		            position: absolute;
		            left: 12px;
		            top: 16px;
		            width: 8px;
		            height: 8px;
		            border-radius: 50%;
		            background: var(--dga-theme-accent);
		        }

		        .dga-footer-top-link {
		            position: absolute;
		            right: 22px;
		            top: 50%;
		            z-index: 2;
		            display: inline-flex;
		            align-items: center;
		            justify-content: center;
		            width: 48px;
		            height: 48px;
		            border: 1px solid rgba(255, 255, 255, .58);
		            border-radius: 8px;
		            background:
		                linear-gradient(135deg, var(--dga-theme-accent), color-mix(in srgb, var(--dga-theme-accent) 76%, #ffffff));
		            color: var(--dga-theme-dark);
		            box-shadow: 0 16px 28px rgba(13, 45, 73, .24);
		            text-decoration: none;
		            transform: translateY(-50%);
		            transition: transform .2s ease, background .2s ease, border-color .2s ease;
		        }

		        .dga-footer-top-link i {
		            display: inline-flex;
		            align-items: center;
		            justify-content: center;
		            width: 30px;
		            height: 30px;
		            border-radius: 8px;
		            background: rgba(255, 255, 255, .58);
		            color: var(--dga-theme-dark);
		            font-size: 15px;
		            transition: transform .2s ease, background .2s ease;
		        }

		        .dga-footer-top-link:hover,
		        .dga-footer-top-link:focus {
		            border-color: rgba(255, 255, 255, .88);
		            background:
		                linear-gradient(135deg, #ffffff, var(--dga-theme-accent));
		            color: var(--dga-theme-dark);
		            transform: translateY(-50%) scale(1.04);
		            outline: none;
		        }

		        .dga-footer-top-link:hover i,
		        .dga-footer-top-link:focus i {
		            background: #ffffff;
		            transform: translateY(-2px);
		        }

		        .dga-home-footer-credit-row {
		            margin: 0;
		            padding: 10px 18px;
		            border: 1px solid rgba(255, 255, 255, .18);
		            border-radius: 8px;
		            background:
		                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-dark) 94%, #061a2a), color-mix(in srgb, var(--dga-theme-secondary) 82%, #082f4f));
		            color: #ffffff;
		            font-size: 14px;
		            font-weight: 900;
		            line-height: 1.35;
		            text-align: center;
		            box-shadow: 0 10px 22px rgba(13, 45, 73, .12);
		        }

		        .dga-footer-credit-row strong,
		        .dga-home-footer-credit-row strong {
		            color: var(--dga-theme-accent);
		        }

		        .dga-footer .container {
		            max-width: 1140px;
		        }

		        .dga-home-footer-strip {
		            grid-template-columns: minmax(360px, .92fr) minmax(460px, 1.08fr);
		            gap: 10px;
		            margin: 10px 0 0;
		            padding: 12px;
		            border-radius: 8px 8px 0 0;
		            background:
		                linear-gradient(115deg, color-mix(in srgb, var(--dga-theme-dark) 94%, #061a2a) 0%, var(--dga-theme-primary) 50%, color-mix(in srgb, var(--dga-theme-secondary) 82%, #082f4f) 100%);
		            box-shadow: 0 -4px 22px rgba(13, 45, 73, .12);
		        }

		        .dga-home-footer-strip::before {
		            height: 4px;
		            background:
		                linear-gradient(90deg, var(--dga-theme-accent), var(--dga-theme-light), var(--dga-theme-accent));
		        }

		        .dga-home-footer-center,
		        .dga-home-footer-meta {
		            min-height: 84px;
		            padding: 14px 16px;
		        }

		        .dga-home-footer-heading {
		            margin-bottom: 8px;
		            font-size: 11px;
		        }

		        .dga-home-footer-heading i,
		        .dga-home-footer-mail i {
		            width: 28px;
		            height: 28px;
		            font-size: 13px;
		        }

		        .dga-home-footer-mail {
		            width: max-content;
		            max-width: 100%;
		            min-height: 36px;
		            margin-top: 0;
		            padding: 4px 12px 4px 4px;
		            cursor: default;
		        }

		        .dga-home-footer-note {
		            margin-top: 8px;
		            font-size: 12px;
		            line-height: 1.45;
		        }

		        .dga-footer-support-list {
		            grid-template-columns: repeat(2, minmax(0, 1fr));
		            gap: 6px 8px;
		        }

		        .dga-footer-support-list li {
		            min-height: 32px;
		            padding: 7px 9px 7px 34px;
		            font-size: 12px;
		            line-height: 1.25;
		        }

		        .dga-footer-support-list li::before {
		            left: 10px;
		            top: 9px;
		            width: 14px;
		            height: 14px;
		        }

		        .dga-footer-top-link {
		            position: fixed;
		            right: 24px;
		            bottom: 24px;
		            top: auto;
		            z-index: 95;
		            width: 40px;
		            height: 40px;
		            border: 1px solid rgba(255, 255, 255, .28);
		            border-radius: 999px;
		            background:
		                linear-gradient(135deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-light) 42%, #ffffff));
		            color: var(--dga-theme-dark);
		            box-shadow: 0 10px 22px rgba(9, 42, 67, .22);
		            transform: none;
		        }

		        .dga-footer-top-link i {
		            width: auto;
		            height: auto;
		            border-radius: 0;
		            background: transparent;
		            color: var(--dga-theme-primary);
		            font-size: 17px;
		        }

		        .dga-footer-top-link:hover,
		        .dga-footer-top-link:focus {
		            background: linear-gradient(135deg, #ffffff, var(--dga-theme-accent));
		            transform: translateY(-3px);
		        }

		        .dga-footer-top-link:hover i,
		        .dga-footer-top-link:focus i {
		            background: transparent;
		            color: var(--dga-theme-dark);
		        }

	        @media (max-width: 991px) {
            .dga-nav {
                justify-content: flex-start;
            }

			            .dept-intro,
			            .dept-layout,
			            .dept-two-col,
			            .institution-panel {
			                grid-template-columns: 1fr;
			            }

		            .dept-fact-strip {
		                padding: 0 24px 24px;
		            }

	            .dept-side {
	                position: static;
            }

	            .dga-department-grid {
	                flex-wrap: wrap;
	                gap: 1px;
	            }

	            .dga-department-item {
	                flex: 1 1 calc(50% - 1px);
	            }

            .dga-department-menu {
                position: static;
                margin-top: 6px;
            }

	            .org-map-inner {
	                min-width: 720px;
	            }

		            .dga-home-footer-strip {
		                grid-template-columns: 1fr;
		                gap: 8px;
		                margin-top: 8px;
		                padding: 10px;
		            }

		            .dga-footer-top-link {
		                position: fixed;
		                right: 18px;
		                bottom: 18px;
		                top: auto;
		            }

		            .dga-footer-top-link:hover,
		            .dga-footer-top-link:focus {
		                transform: translateY(-2px);
		            }
	        }

	        @media (max-width: 575px) {
	            .dept-title-panel h2 {
	                font-size: 27px;
	            }

		            .dept-title-panel {
		                padding: 24px 22px 26px 28px;
		            }

		            .dept-title-panel::before {
		                position: static;
		                display: inline-flex;
		                margin-top: 12px;
		            }

		            .dept-fact-strip {
		                padding: 0 18px 18px;
		            }

		            .dga-department-grid {
		                display: block;
		            }

		            .dga-home-footer-strip {
		                width: 100%;
		                margin: 8px 0 0;
		                padding: 10px;
		                border-radius: 8px 8px 0 0;
		            }

		            .dga-home-footer-center,
		            .dga-home-footer-meta {
		                min-height: 0;
		                padding: 10px 12px;
		            }

		            .dga-home-footer-heading {
		                align-items: flex-start;
		            }

		            .dga-footer-top-link {
		                right: 14px;
		                bottom: 14px;
		                width: 38px;
		                height: 38px;
		            }

		            .dga-footer-top-link i {
		                font-size: 14px;
		            }
		        }
		    .dga-department-strip {
		        border-top: 1px solid color-mix(in srgb, var(--dga-theme-light) 20%, transparent);
		        border-bottom: 1px solid color-mix(in srgb, var(--dga-theme-dark) 36%, transparent);
		        box-shadow: 0 8px 18px var(--dga-theme-shadow);
		    }

		    .dga-department-strip::before {
		        display: none;
		    }

		    .dga-department-strip::after {
		        height: 1px;
		        opacity: .65;
		    }

		    .dga-department-strip .container {
		        max-width: none;
		        padding-left: clamp(18px, 3vw, 54px);
		        padding-right: clamp(18px, 3vw, 54px);
		    }

		    .dga-department-grid {
		        display: flex;
		        align-items: stretch;
		        justify-content: center;
		        gap: 0;
		        padding: 0;
		        overflow: visible;
		    }

		    .dga-department-item {
		        position: relative;
		        flex: 0 0 auto;
		    }

		    .dga-department-title,
		    .dga-department-title:hover,
		    .dga-department-title:focus {
		        min-height: 44px;
		        padding: 0 18px;
		        border: 0;
		        border-left: 1px solid color-mix(in srgb, #ffffff 14%, transparent);
		        border-radius: 0;
		        background: transparent;
		        color: rgba(255, 255, 255, .94);
		        gap: 7px;
		        transform: none;
		    }

		    .dga-department-item:last-child .dga-department-title {
		        border-right: 1px solid color-mix(in srgb, #ffffff 14%, transparent);
		    }

		    .dga-department-title::after {
		        left: 0;
		        right: 0;
		        bottom: 0;
		        height: 3px;
		        border-radius: 0;
		        opacity: 0;
		        transform: scaleX(0);
		    }

		    .dga-department-title strong {
		        font-size: 13px;
		        font-weight: 900;
		        letter-spacing: 0;
		        text-transform: uppercase;
		    }

		    .dga-department-arrow {
		        width: auto;
		        height: auto;
		        border-radius: 0;
		        background: transparent;
		        font-size: 10px;
		    }

		    .dga-department-title:hover,
		    .dga-department-title:focus,
		    .dga-department-title.is-active,
		    .dga-department-item.is-open .dga-department-title {
		        color: #ffffff;
		        box-shadow: inset 0 -3px 0 var(--dga-theme-accent);
		    }

		    .dga-department-title:hover::after,
		    .dga-department-title:focus::after,
		    .dga-department-title.is-active::after,
		    .dga-department-item.is-open .dga-department-title::after {
		        opacity: 1;
		        transform: scaleX(1);
		    }

		    .dga-department-title:hover .dga-department-arrow,
		    .dga-department-title:focus .dga-department-arrow,
		    .dga-department-title.is-active .dga-department-arrow,
		    .dga-department-item:hover .dga-department-arrow,
		    .dga-department-item:focus-within .dga-department-arrow {
		        background: transparent;
		        color: inherit;
		        transform: rotate(180deg);
		    }

		    .dga-department-menu {
		        left: 0;
		        top: 100%;
		        min-width: 250px;
		        padding: 0;
		        border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 28%, #ffffff);
		        border-top: 0;
		        border-radius: 0 0 8px 8px;
		        background: color-mix(in srgb, var(--dga-theme-dark) 96%, #000000);
		        box-shadow: 0 16px 34px rgba(7, 30, 49, .22);
		        overflow: hidden;
		    }

		    .dga-department-menu a {
		        min-height: 45px;
		        display: flex;
		        align-items: center;
		        padding: 0 18px 0 22px;
		        border: 0;
		        border-radius: 0;
		        border-bottom: 1px solid color-mix(in srgb, #ffffff 12%, transparent);
		        background: transparent;
		        color: rgba(255, 255, 255, .92);
		        font-size: 13px;
		        font-weight: 900;
		        letter-spacing: 0;
		        text-transform: uppercase;
		    }

		    .dga-department-menu a:last-child {
		        border-bottom: 0;
		    }

		    .dga-department-menu a:hover,
		    .dga-department-menu a:focus {
		        background: color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
		        color: #ffffff;
		    }

		    @media (max-width: 991px) {
		        .dga-department-strip .container {
		            padding-left: 12px;
		            padding-right: 12px;
		        }

		        .dga-department-grid {
		            display: grid;
		            grid-template-columns: repeat(2, minmax(0, 1fr));
		            gap: 1px;
		        }

		        .dga-department-title,
		        .dga-department-title:hover,
		        .dga-department-title:focus {
		            width: 100%;
		            min-height: 38px;
		            padding: 0 12px;
		            border: 0;
		            justify-content: center;
		        }

		        .dga-department-menu {
		            position: static;
		            width: 100%;
		            min-width: 0;
		            margin-top: 0;
		            transform: none;
		        }
		    }
        .dga-nav {
            justify-content: flex-end;
            gap: 0;
            display: inline-flex;
            width: auto;
            margin-left: auto;
            overflow: hidden;
            border: 1px solid var(--dga-theme-border);
            border-radius: 8px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .1);
        }

        .dga-nav-link,
        .dga-nav-link:hover,
        .dga-nav-link:focus {
            min-height: 38px;
            padding: 0 16px;
            border: 0;
            border-right: 1px solid var(--dga-theme-border);
            border-radius: 0;
            background: transparent;
            color: var(--dga-theme-heading);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
            box-shadow: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .dga-nav-link:last-child {
            border-right: 0;
        }

        .dga-nav-link i {
            width: 15px;
            text-align: center;
            color: var(--dga-theme-accent);
            font-size: .82rem;
        }

        .dga-nav-link:hover,
        .dga-nav-link:focus {
            background: var(--dga-theme-soft);
            color: var(--dga-theme-primary-dark);
        }

        .dga-nav-link.primary,
        .dga-nav-link.primary:hover,
        .dga-nav-link.primary:focus {
            background: var(--dga-theme-primary);
            color: #fff;
        }

        .dga-nav-link.primary i {
            color: var(--dga-theme-accent);
        }

        .dga-department-menu {
            left: 0;
            right: auto;
            top: calc(100% + 1px);
            min-width: 268px;
            padding: 5px 0;
            border: 1px solid rgba(255, 255, 255, .16);
            border-left: 4px solid var(--dga-theme-accent);
            border-radius: 0 0 8px 0;
            text-align: left;
            transform-origin: left top;
        }

        .dga-department-menu a,
        .dga-department-menu a:nth-child(n) {
            min-height: 42px;
            padding: 10px 18px 10px 16px;
            justify-content: flex-start;
            text-align: left;
            line-height: 1.25;
            border-bottom: 1px solid rgba(255, 255, 255, .11);
            box-shadow: none;
        }

        .dga-department-menu a:hover,
        .dga-department-menu a:focus {
            padding-left: 20px;
            background: var(--dga-theme-primary-dark);
            box-shadow: inset 4px 0 0 var(--dga-theme-accent);
        }

        .dga-department-grid {
            gap: clamp(16px, 2.4vw, 34px);
            padding: 0 120px;
            position: relative;
        }

        .dga-department-title,
        .dga-department-title:hover,
        .dga-department-title:focus {
            min-width: 0;
            min-height: 42px;
            padding: 6px 2px 8px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: rgba(255, 255, 255, .9);
            box-shadow: none;
            backdrop-filter: none;
            transform: none;
        }

        .dga-department-title::before,
        .dga-department-title::after {
            content: "";
            left: 0;
            right: 0;
            bottom: 5px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, var(--dga-theme-accent), transparent);
            opacity: 0;
            transform: scaleX(.45);
            transform-origin: center;
            transition: opacity .2s ease, transform .2s ease;
        }

        .dga-department-title strong {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
        }

        .dga-department-arrow {
            color: rgba(255, 244, 199, .9);
            opacity: .72;
            transition: transform .2s ease, opacity .2s ease, color .2s ease;
        }

        .dga-department-title:hover,
        .dga-department-title:focus,
        .dga-department-title.is-active,
        .dga-department-item.is-open .dga-department-title {
            /* background: transparent; */
            border-color: transparent;
            color: #ffffff;
            box-shadow: none;
            transform: none;
        }

        .dga-department-title:hover::before,
        .dga-department-title:focus::before,
        .dga-department-title.is-active::before,
        .dga-department-item.is-open .dga-department-title::before,
        .dga-department-title:hover::after,
        .dga-department-title:focus::after,
        .dga-department-title.is-active::after,
        .dga-department-item.is-open .dga-department-title::after {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-department-title:hover .dga-department-arrow,
        .dga-department-title:focus .dga-department-arrow,
        .dga-department-title.is-active .dga-department-arrow,
        .dga-department-item.is-open .dga-department-arrow {
            color: var(--dga-theme-accent);
            opacity: 1;
            transform: rotate(180deg);
        }

        .dga-department-menu {
            left: 8px;
            right: auto;
            top: calc(100% + 7px);
            min-width: 286px;
            padding: 12px 10px;
            border: 1px solid rgba(47, 117, 181, .24);
            border-radius: 14px;
            background:
                linear-gradient(145deg,
                    color-mix(in srgb, var(--dga-theme-light) 72%, #ffffff) 0%,
                    #ffffff 54%,
                    color-mix(in srgb, var(--dga-theme-soft) 64%, #ffffff) 100%);
            box-shadow: 0 20px 46px rgba(6, 39, 68, .18);
            text-align: left;
            transform-origin: left top;
            transform: translateY(10px);
            backdrop-filter: blur(14px);
            overflow: visible;
        }

        .dga-department-item:hover .dga-department-menu,
        .dga-department-item:focus-within .dga-department-menu,
        .dga-department-item.is-open .dga-department-menu {
            transform: translateY(0);
        }

        .dga-department-menu::before {
            display: block;
            content: "";
            position: absolute;
            top: -6px;
            left: 28px;
            width: 12px;
            height: 12px;
            border-top: 1px solid rgba(47, 117, 181, .24);
            border-left: 1px solid rgba(47, 117, 181, .24);
            background: rgba(248, 252, 255, .98);
            transform: rotate(45deg);
        }

        .dga-department-menu a,
        .dga-department-menu a:nth-child(n) {
            min-height: 38px;
            margin: 0;
            padding: 9px 18px 9px 28px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: var(--dga-theme-heading);
            font-size: 12.5px;
            font-weight: 800;
            text-transform: none;
            letter-spacing: 0;
            box-shadow: none;
        }

        .dga-department-menu a::before {
            display: block;
            content: "";
            position: absolute;
            left: 16px;
            top: 50%;
            width: 4px;
            height: 20px;
            border-radius: 999px;
            background: var(--dga-theme-accent);
            color: transparent;
            transform: translateY(-50%) scaleY(.38);
            transition: transform .18s ease, background .18s ease;
        }

        .dga-department-menu a:nth-child(1) {
            background: linear-gradient(90deg, #eaf4ff 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(2) {
            background: linear-gradient(90deg, #e9f7ef 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(3) {
            background: linear-gradient(90deg, #fff4df 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(4) {
            background: linear-gradient(90deg, #f2efff 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(5) {
            background: linear-gradient(90deg, #edf8f5 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(6) {
            background: linear-gradient(90deg, #fff0ec 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(7) {
            background: linear-gradient(90deg, #eef7ff 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(n+8) {
            background: linear-gradient(90deg, #f7f3e8 0%, rgba(255, 255, 255, .18) 100%);
        }

        .dga-department-menu a:nth-child(n) {
            position: relative;
            opacity: 0;
            margin-bottom: 5px;
            border: 1px solid rgba(47, 117, 181, .2);
            border-radius: 9px;
            background: linear-gradient(90deg, rgba(255, 255, 255, .98), rgba(238, 248, 255, .9));
            color: #123451;
            translate: 0 8px;
            transform-origin: left center;
            box-shadow: 0 8px 16px rgba(6, 39, 68, .06);
            transition: color .18s ease, background .18s ease, padding-left .18s ease, transform .18s ease, box-shadow .18s ease, filter .18s ease, border-color .18s ease;
        }

        .dga-department-menu a:nth-child(1) {
            --dga-row-accent: #2f75b5;
            background: linear-gradient(90deg, #ffffff 0%, #eef7ff 100%);
        }

        .dga-department-menu a:nth-child(2) {
            --dga-row-accent: #207985;
            background: linear-gradient(90deg, #ffffff 0%, #edf9f7 100%);
        }

        .dga-department-menu a:nth-child(3) {
            --dga-row-accent: #c18a22;
            background: linear-gradient(90deg, #ffffff 0%, #fff6df 100%);
        }

        .dga-department-menu a:nth-child(4) {
            --dga-row-accent: #2868a6;
            background: linear-gradient(90deg, #ffffff 0%, #f0f5ff 100%);
        }

        .dga-department-menu a:nth-child(5) {
            --dga-row-accent: #258c86;
            background: linear-gradient(90deg, #ffffff 0%, #ecfaf5 100%);
        }

        .dga-department-menu a:nth-child(6) {
            --dga-row-accent: #d0a03d;
            background: linear-gradient(90deg, #ffffff 0%, #fff8e8 100%);
        }

        .dga-department-menu a:nth-child(7) {
            --dga-row-accent: #227b75;
            background: linear-gradient(90deg, #ffffff 0%, #edf9f7 100%);
        }

        .dga-department-menu a:nth-child(n+8) {
            --dga-row-accent: #2d877d;
            background: linear-gradient(90deg, #ffffff 0%, #eff8ff 100%);
        }

        .dga-department-menu a:last-child {
            margin-bottom: 0;
        }

        .dga-department-item:hover .dga-department-menu a,
        .dga-department-item:focus-within .dga-department-menu a,
        .dga-department-item.is-open .dga-department-menu a {
            animation: dgaDropdownStepIn .34s ease forwards;
        }

        .dga-department-menu a:nth-child(1) { animation-delay: .02s; }
        .dga-department-menu a:nth-child(2) { animation-delay: .06s; }
        .dga-department-menu a:nth-child(3) { animation-delay: .10s; }
        .dga-department-menu a:nth-child(4) { animation-delay: .14s; }
        .dga-department-menu a:nth-child(5) { animation-delay: .18s; }
        .dga-department-menu a:nth-child(6) { animation-delay: .22s; }
        .dga-department-menu a:nth-child(7) { animation-delay: .26s; }
        .dga-department-menu a:nth-child(n+8) { animation-delay: .30s; }

        .dga-department-menu a:hover,
        .dga-department-menu a:focus {
            padding-left: 34px;
            border-color: rgba(47, 117, 181, .28);
            background: linear-gradient(135deg, #174f82 0%, #2f75b5 50%, #207985 100%);
            color: #ffffff;
            opacity: 1;
            translate: 0 0;
            transform: scale(1.025);
            filter: none;
            box-shadow: 0 14px 28px rgba(6, 39, 68, .18), inset 4px 0 0 var(--dga-row-accent, var(--dga-theme-accent));
            z-index: 2;
        }

        .dga-department-menu a:hover::before,
        .dga-department-menu a:focus::before {
            background: #f0b84f;
            transform: translateY(-50%) scaleY(1);
        }

        @keyframes dgaDropdownStepIn {
            from {
                opacity: 0;
                translate: 0 8px;
            }

            to {
                opacity: 1;
                translate: 0 0;
            }
        }

	        .dga-department-body .dga-topbar {
	            position: fixed;
	            left: 0;
	            right: 0;
	            top: 0;
	            z-index: 80;
	            background: linear-gradient(90deg, var(--dga-theme-dark) 0%, var(--dga-theme-primary) 58%, var(--dga-theme-secondary) 100%);
	            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .16);
	        }

	        .dga-department-body .dga-header {
	            position: fixed;
	            left: 0;
	            right: 0;
	            top: var(--dga-topbar-height, 36px);
	            z-index: 70;
	            background: rgba(255, 255, 255, .98);
	            border-bottom: 1px solid #c8e0ef;
	            box-shadow: 0 12px 30px rgba(32, 54, 74, .1);
	        }

	        .dga-department-body {
	            padding-top: var(--dga-fixed-top-height, 164px);
	        }

        .dga-department-body .dga-topbar .container,
        .dga-department-body .dga-header .container,
        .dga-department-body .dga-department-strip .container {
            width: 100%;
            max-width: none;
            margin-left: 0;
            margin-right: 0;
            padding-left: clamp(18px, 2.6vw, 52px);
            padding-right: clamp(18px, 2.6vw, 52px);
        }

        .dga-department-body .dga-topbar .container {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .dga-department-body .dga-header .row {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .dga-department-body .dga-header .row > [class*="col-"] {
            padding-left: 0;
            padding-right: 0;
        }

        .dga-department-body .dga-nav {
            justify-content: flex-end;
            gap: 8px;
            display: flex;
            width: auto;
            margin-left: auto;
            overflow: hidden;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .dga-department-body .dga-nav-link,
        .dga-department-body .dga-nav-link:hover,
        .dga-department-body .dga-nav-link:focus {
            min-height: 40px;
            padding: 0 18px;
            border: 1px solid rgba(47, 117, 181, .18);
            border-radius: 8px;
            background: rgba(255, 255, 255, .96);
            color: var(--dga-theme-heading);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .08);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .dga-department-body .dga-nav-link i {
            width: 15px;
            text-align: center;
            color: var(--dga-theme-accent);
            font-size: .82rem;
        }

        .dga-department-body .dga-nav-link:hover,
        .dga-department-body .dga-nav-link:focus {
            background: var(--dga-theme-soft);
            color: var(--dga-theme-primary-dark);
        }

        .dga-department-body .dga-nav-link.primary,
        .dga-department-body .dga-nav-link.primary:hover,
        .dga-department-body .dga-nav-link.primary:focus {
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-dark));
            color: #fff;
            box-shadow: 0 12px 24px rgba(13, 45, 73, .18);
        }

        .dga-department-body .dga-department-strip {
            position: relative;
            overflow: visible;
            border-top: 1px solid rgba(255, 255, 255, .22);
            background: linear-gradient(90deg, var(--dga-theme-dark) 0%, var(--dga-theme-primary) 46%, var(--dga-theme-secondary) 100%);
            box-shadow: 0 10px 24px var(--dga-theme-shadow);
        }

        .dga-department-body .dga-department-strip::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255, 244, 199, .14), transparent 24% 72%, rgba(155, 224, 213, .16));
            pointer-events: none;
        }

        .dga-department-body .dga-department-strip::after {
            height: 3px;
            background: linear-gradient(90deg, var(--dga-theme-accent) 0 26%, #ffffff 26% 54%, var(--dga-theme-light) 54% 100%);
        }

	        .dga-department-body .dga-department-grid {
	            position: relative;
	            justify-content: flex-start;
	            /* gap: 9px; */
	            padding: 2px 0 2px clamp(72px, 8vw, 132px);
	        }

	        .dga-department-body .dga-footer {
	            padding: 20px 0 18px;
	            background:
	                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-dark) 90%, #071e31), var(--dga-theme-primary));
	            color: #fff;
	        }

        .dga-department-body .dga-footer .container {
            max-width: none;
            padding-left: clamp(18px, 2.6vw, 52px);
            padding-right: clamp(18px, 2.6vw, 52px);
        }

        .dga-department-body .dept-side .dga-hero-menu {
            overflow: visible;
        }

        .dga-department-body .dept-side .dga-hero-menu-button,
        .dga-department-body .dept-side .dga-hero-menu-button:nth-child(n) {
            transform-origin: center;
            transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease, color .18s ease;
        }

        .dga-department-body .dept-side .dga-hero-menu-button::after {
            width: 100%;
            opacity: 0;
            transition: opacity .16s ease;
        }

        .dga-department-body .dept-side .dga-hero-menu-button:hover,
        .dga-department-body .dept-side .dga-hero-menu-button:focus {
            transform: scale(1.085) !important;
        }

        .dga-department-body .dept-side .dga-hero-menu-button.is-active {
            transform: scale(1.04) !important;
        }

        .dga-department-body .dept-side .dga-hero-menu-button.is-active:hover,
        .dga-department-body .dept-side .dga-hero-menu-button.is-active:focus {
            transform: scale(1.095) !important;
        }

        .dga-department-body .dept-side .dga-hero-menu-button:hover::after,
        .dga-department-body .dept-side .dga-hero-menu-button:focus::after,
        .dga-department-body .dept-side .dga-hero-menu-button.is-active::after {
            width: 100%;
            opacity: 1;
        }

        @media (max-width: 991px) {
            .dga-nav {
                width: 100%;
                justify-content: stretch;
            }

            .dga-nav-link {
                flex: 1 1 0;
                justify-content: center;
                padding: 0 12px;
            }

            .dga-department-menu {
                min-width: 100%;
                border-radius: 0;
                transform: none;
            }

            .dga-department-item:hover .dga-department-menu,
            .dga-department-item:focus-within .dga-department-menu,
            .dga-department-item.is-open .dga-department-menu {
                transform: none;
            }
        }
	    </style>
	    <link rel="stylesheet" href="{{ asset('site/css/dga-responsive.css') }}?v=20260724q">
</head>

<body class="dga-department-body dga-department-{{ $department['slug'] }}" id="dga-page-top">
    @include('dga.partials.header', [
        'departments' => $departments,
        'activeDepartmentSlug' => $department['slug'],
        'homeHref' => url('/dga'),
        'homeLabel' => 'DGA',
        'navLabel' => 'DGA department navigation',
    ])

    @php
        $structureNodeCount = count($department['map']);
        if (!empty($department['structure_map'])) {
            if (!empty($department['structure_map']['left'])) {
                $structureNodeCount = 2 + count($department['structure_map']['left']) + count($department['structure_map']['right']) + count($department['structure_map']['common']);
            } else {
                $structureNodeCount = 2;
                foreach ($department['structure_map']['branches'] as $branch) {
                    $structureNodeCount += count($branch['items']);
                }
            }
        }

        $lfaVisualCards = [
            [
                'label' => 'Municipal governance in India',
                'image' => 'assets/images/lfa.png',
                'photo' => true,
            ],
        ];
    @endphp
			    <main class="dept-page dga-page-sections-band">
		        <div class="container-fluid px-lg-5 dga-page-sections-inner">
            <section class="dept-intro">
                <div class="dept-title-panel">
                    {{-- <span class="dept-kicker">{{ $department['short_name'] }}</span> --}}
                    <h2>{{ $department['name'] }}</h2>
                    <p>{{ $department['summary'] }}</p>
                </div>
		                <aside class="dept-fact-strip {{ $department['slug'] === 'lfa' ? 'lfa-visual-strip' : '' }}" aria-label="Department quick facts">
		                    @if ($department['slug'] === 'lfa')
			                        @foreach ($lfaVisualCards as $card)
			                            <div class="lfa-visual-card">
			                                <div class="lfa-visual-media {{ $card['photo'] ? 'is-photo' : '' }}">
			                                    <img src="{{ asset($card['image']) }}?v=2" alt="{{ $card['label'] }}">
			                                </div>
			                            </div>
			                        @endforeach
		                    @else
		                        <div class="dept-fact">
		                            <strong>{{ $department['short_name'] }}</strong>
		                            <span>Department code</span>
		                        </div>
		                        <div class="dept-fact">
		                            <strong>{{ count($department['institutions']) }}</strong>
		                            <span>Institution coverage groups</span>
		                        </div>
		                        <div class="dept-fact">
		                            <strong>{{ $structureNodeCount }}</strong>
		                            <span>Mindmap nodes</span>
		                        </div>
		                    @endif
		                </aside>
		            </section>

	            <section class="dept-layout dga-hero-section-grid" aria-label="Department page sections">
		                    <aside class="dept-side dga-hero-menu-card" aria-label="Department navigator">
		                    <h3 class="dga-hero-menu-title">Department Navigator</h3>
		                    <div class="dga-hero-menu" aria-label="Department content menu">
		                    @if (!empty($department['nav_menu']))
		                        @php
		                            $navItems = collect($department['nav_menu']);
		                            $navTopLevel = $navItems->filter(fn ($i) => empty($i['parent_id']))->values();
		                        @endphp
		                        @foreach ($navTopLevel as $navIndex => $navItem)
		                            @php
		                                $navChildren = $navItems->filter(fn ($i) => (int) ($i['parent_id'] ?? 0) === (int) $navItem['id'])->values();
		                            @endphp
		                            <div class="dga-hero-menu-group {{ $navChildren->count() ? 'has-children' : '' }}">
		                                <a class="dga-hero-menu-button {{ $navIndex === 0 ? 'is-active' : '' }}" href="{{ $navItem['target'] }}" @if ($navIndex === 0) aria-current="true" @endif @if ($navChildren->count()) data-dga-menu-toggle @endif>
		                                    @if (!empty($navItem['icon']))
		                                        <i class="{{ $navItem['icon'] }}" aria-hidden="true"></i>
		                                    @endif
		                                    <span>{{ $navItem['label'] }}</span>
		                                </a>
		                                @if ($navChildren->count())
		                                    <div class="dga-hero-menu-children">
		                                        @foreach ($navChildren as $navChild)
		                                            <a class="dga-hero-menu-button dga-hero-menu-child" href="{{ $navChild['target'] }}">
		                                                @if (!empty($navChild['icon']))
		                                                    <i class="{{ $navChild['icon'] }}" aria-hidden="true"></i>
		                                                @endif
		                                                <span>{{ $navChild['label'] }}</span>
		                                            </a>
		                                        @endforeach
		                                    </div>
		                                @endif
		                            </div>
		                        @endforeach
		                        <style>
		                            .dga-hero-menu-children { display: none; flex-direction: column; }
		                            .dga-hero-menu-group:hover .dga-hero-menu-children,
		                            .dga-hero-menu-group.is-expanded .dga-hero-menu-children { display: flex; }
		                            .dga-hero-menu-child { padding-left: 2.5rem; }
		                        </style>
		                    @else
		                        <a class="dga-hero-menu-button is-active" href="#structure" aria-current="true">
		                            <i class="fas fa-sitemap" aria-hidden="true"></i>
		                            <span>Structure Map</span>
			                        </a>
			                        <a class="dga-hero-menu-button" href="#establishment">
			                            <i class="fas fa-building" aria-hidden="true"></i>
			                            <span>Establishment</span>
			                        </a>
	                    <a class="dga-hero-menu-button" href="#acts">
	                        <i class="fas fa-book" aria-hidden="true"></i>
	                        <span>Acts and Rules</span>
	                    </a>
	                    <a class="dga-hero-menu-button" href="#functions">
	                        <i class="fas fa-tasks" aria-hidden="true"></i>
	                        <span>Functions</span>
	                    </a>
		                    @if (!empty($department['regions']))
		                        <a class="dga-hero-menu-button" href="#regions">
		                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
		                            <span>Regional Map</span>
		                        </a>
	                    @endif
	                    <a class="dga-hero-menu-button" href="#institutions">
	                        <i class="fas fa-university" aria-hidden="true"></i>
	                        <span>Institutions</span>
	                    </a>
	                    <a class="dga-hero-menu-button" href="#highlights">
	                        <i class="fas fa-star" aria-hidden="true"></i>
	                        <span>Highlights</span>
	                    </a>
	                    <a class="dga-hero-menu-button" href="#contact">
	                        <i class="fas fa-address-card" aria-hidden="true"></i>
	                        <span>Contact</span>
	                    </a>
	                    @endif
	                    </div>
	                </aside>

		                <div class="dept-content dga-hero-content-shell is-open">
	                    <section class="dept-block" id="structure">
	                        <h3><span>Mind Map</span>Organizational Structure</h3>
	                        @if (!empty($department['structure_image']))
	                            <div class="document-map">
	                                <img src="{{ asset($department['structure_image']) }}" alt="{{ $department['name'] }} organizational structure">
	                            </div>
	                        @elseif (!empty($department['structure_map']))
		                            <div class="org-map {{ $department['slug'] === 'lfa' ? 'is-lfa-flow' : '' }}" aria-label="{{ $department['short_name'] }} organizational structure">
	                                <div class="org-map-inner">
	                                    @if (!empty($department['structure_map']['left']))
	                                        <div class="org-level">
	                                            <div class="org-node org-root popup-down" tabindex="0">
	                                                <span class="org-node-title">{{ $department['structure_map']['root']['title'] }}</span>
	                                                <div class="org-popup">
	                                                    <strong>{{ $department['structure_map']['root']['title'] }}</strong>
	                                                    <p>{{ $department['structure_map']['root']['detail'] }}</p>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="org-link"></div>
	                                        <div class="org-level">
	                                            <div class="org-node org-lead popup-down" tabindex="0">
	                                                <span class="org-node-title">{{ $department['structure_map']['lead']['title'] }}</span>
	                                                <div class="org-popup">
	                                                    <strong>{{ $department['structure_map']['lead']['title'] }}</strong>
	                                                    @if (!empty($department['structure_map']['lead']['people']))
	                                                        <div class="org-table-scroll">
	                                                            <table>
	                                                                <thead>
	                                                                    <tr>
	                                                                        <th>Sl. No</th>
	                                                                        <th>Name (Designation)</th>
	                                                                        <th>Office Address</th>
	                                                                        <th>Phone Number</th>
	                                                                        <th>Email-ID</th>
	                                                                    </tr>
	                                                                </thead>
	                                                                <tbody>
	                                                                    @foreach ($department['structure_map']['lead']['people'] as $person)
	                                                                        <tr>
	                                                                            <td>{{ $person['sl'] }}</td>
	                                                                            <td>{{ $person['name'] }}<br><small>{{ $person['designation'] }}</small></td>
	                                                                            <td>{{ $person['address'] }}</td>
	                                                                            <td>{{ $person['phone'] }}</td>
	                                                                            <td>{{ $person['email'] }}</td>
	                                                                        </tr>
	                                                                    @endforeach
	                                                                </tbody>
	                                                            </table>
	                                                        </div>
	                                                    @else
	                                                        <p>{{ $department['structure_map']['lead']['detail'] }}</p>
	                                                    @endif
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <div class="org-link"></div>
	                                        <div class="org-split">
	                                            <div class="org-stream is-left">
	                                                @foreach ($department['structure_map']['left'] as $node)
	                                                    <div class="org-node" tabindex="0">
	                                                        <span class="org-node-title">{{ $node['title'] }}</span>
	                                                        <div class="org-popup">
	                                                            <strong>{{ $node['title'] }}</strong>
	                                                            @if (!empty($node['people']))
	                                                                <div class="org-table-scroll">
	                                                                    <table>
	                                                                        <thead>
	                                                                            <tr>
	                                                                                <th>Sl. No</th>
	                                                                                <th>Name (Designation)</th>
	                                                                                <th>Office Address</th>
	                                                                                <th>Phone Number</th>
	                                                                                <th>Email-ID</th>
	                                                                            </tr>
	                                                                        </thead>
	                                                                        <tbody>
	                                                                            @foreach ($node['people'] as $person)
	                                                                                <tr>
	                                                                                    <td>{{ $person['sl'] }}</td>
	                                                                                    <td>{{ $person['name'] }}<br><small>{{ $person['designation'] }}</small></td>
	                                                                                    <td>{{ $person['address'] }}</td>
	                                                                                    <td>{{ $person['phone'] }}</td>
	                                                                                    <td>{{ $person['email'] }}</td>
	                                                                                </tr>
	                                                                            @endforeach
	                                                                        </tbody>
	                                                                    </table>
	                                                                </div>
	                                                            @else
	                                                                <p>{{ $node['detail'] }}</p>
	                                                            @endif
	                                                        </div>
	                                                    </div>
	                                                    <div class="org-link"></div>
	                                                @endforeach
	                                            </div>
	                                            <div class="org-stream is-right">
	                                                @foreach ($department['structure_map']['right'] as $node)
	                                                    <div class="org-node" tabindex="0">
	                                                        <span class="org-node-title">{{ $node['title'] }}</span>
	                                                        <div class="org-popup">
	                                                            <strong>{{ $node['title'] }}</strong>
	                                                            @if (!empty($node['people']))
	                                                                <div class="org-table-scroll">
	                                                                    <table>
	                                                                        <thead>
	                                                                            <tr>
	                                                                                <th>Sl. No</th>
	                                                                                <th>Name (Designation)</th>
	                                                                                <th>Office Address</th>
	                                                                                <th>Phone Number</th>
	                                                                                <th>Email-ID</th>
	                                                                            </tr>
	                                                                        </thead>
	                                                                        <tbody>
	                                                                            @foreach ($node['people'] as $person)
	                                                                                <tr>
	                                                                                    <td>{{ $person['sl'] }}</td>
	                                                                                    <td>{{ $person['name'] }}<br><small>{{ $person['designation'] }}</small></td>
	                                                                                    <td>{{ $person['address'] }}</td>
	                                                                                    <td>{{ $person['phone'] }}</td>
	                                                                                    <td>{{ $person['email'] }}</td>
	                                                                                </tr>
	                                                                            @endforeach
	                                                                        </tbody>
	                                                                    </table>
	                                                                </div>
	                                                            @else
	                                                                <p>{{ $node['detail'] }}</p>
	                                                            @endif
	                                                        </div>
	                                                    </div>
	                                                    <div class="org-link"></div>
	                                                @endforeach
	                                            </div>
	                                        </div>
	                                        <div class="org-common">
	                                            @foreach ($department['structure_map']['common'] as $index => $node)
	                                                <div class="org-node" tabindex="0">
	                                                    @if ($index === 0)
	                                                        <span class="org-side-arrow left"></span>
	                                                        <span class="org-side-arrow right"></span>
	                                                    @endif
	                                                    <span class="org-node-title">{{ $node['title'] }}</span>
	                                                    <div class="org-popup">
	                                                        <strong>{{ $node['title'] }}</strong>
	                                                        <p>{{ $node['detail'] }}</p>
	                                                    </div>
	                                                </div>
	                                                @if (!$loop->last)
	                                                    <div class="org-link"></div>
	                                                @endif
	                                            @endforeach
	                                        </div>
	                                    @else
	                                        <div class="org-level">
	                                            <div class="org-node org-root">{{ $department['structure_map']['root'] }}</div>
	                                        </div>
	                                        <div class="org-link"></div>
	                                        <div class="org-level">
	                                            <div class="org-node org-lead">{{ $department['structure_map']['lead'] }}</div>
	                                        </div>
	                                    @endif
	                                </div>
	                            </div>
	                        @else
	                            <div class="mindmap">
	                                @foreach ($department['map'] as $index => $node)
	                                    <div class="mindmap-node">
	                                        <strong>{{ $index + 1 }}</strong>
	                                        <p class="mb-0">{{ $node }}</p>
	                                    </div>
	                                @endforeach
	                            </div>
		                        @endif
		                    </section>

	                    <div class="dept-two-col">
                        <section class="dept-block" id="establishment">
                            <h3><span>Establishment</span>Evolution and authority</h3>
                            <ul class="dga-list">
                                @foreach ($department['establishment'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul>
                        </section>

                        <section class="dept-block" id="acts">
                            <h3><span>Acts and Rules</span>Statutory basis</h3>
                            <ul class="dga-list">
                                @foreach ($department['acts'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul>
                        </section>
                    </div>

                    <section class="dept-block" id="functions">
                        <h3><span>Functions</span>Department responsibilities</h3>
                        <ul class="dga-list">
                            @foreach ($department['functions'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </section>

                    @if (!empty($department['regions']))
                        <section class="dept-block" id="regions">
                            <h3><span>Regional Map</span>Coverage distribution</h3>
                            <div class="region-grid">
                                @foreach ($department['regions'] as $region)
                                    <div class="dept-region">
                                        <h4>{{ $region['name'] }}</h4>
                                        <p class="mb-0">{{ implode(', ', $region['places']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

	                    <section class="dept-block" id="institutions">
	                        <h3><span>Auditable Institutions</span>Coverage numbers</h3>
	                        @php
	                            $totalInstitution = $department['institutions'][0] ?? null;
	                            $institutionGroups = array_slice($department['institutions'], 1);
	                        @endphp
	                        <div class="institution-panel">
	                            @if ($totalInstitution)
	                                <div class="institution-total">
	                                    <span>Coverage Total</span>
	                                    <strong>{{ $totalInstitution['value'] }}</strong>
	                                    <p>{{ $totalInstitution['label'] }} under the audit coverage of {{ $department['short_name'] }}.</p>
	                                </div>
	                            @endif
	                            <div class="stat-grid">
	                                @foreach ($institutionGroups as $item)
	                                    <div class="dept-stat">
	                                        <strong>{{ $item['value'] }}</strong>
	                                        <span>{{ $item['label'] }}</span>
	                                    </div>
	                                @endforeach
	                            </div>
	                        </div>
	                    </section>

                    <div class="dept-two-col">
                        <section class="dept-block" id="highlights">
                            <h3><span>Highlights</span>Achievements and initiatives</h3>
                            <ul class="dga-list">
                                @foreach ($department['achievements'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul>
                        </section>

                        <section class="dept-block" id="contact">
                            <h3><span>Contact</span>Office details</h3>
                            @if (!empty($department['contact']))
                                <ul class="dga-list">
                                    @foreach ($department['contact'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0">Contact details will be updated from the department source document when available.</p>
                            @endif
                        </section>

                        @foreach ($department['custom_block'] ?? [] as $customBlock)
                            <section class="dept-block" id="custom-{{ $customBlock['id'] }}">
                                <h3><span>{{ $customBlock['title'] ?? '' }}</span></h3>
                                @foreach (preg_split('/\r\n|\r|\n/', (string) ($customBlock['body'] ?? '')) as $paragraph)
                                    @continue(trim($paragraph) === '')
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </section>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </main>

				    @include('dga.partials.footer', [
				        'footerTitle' => 'Designed and Developed by NIC &copy; 2026',
				        'footerSubtitle' => '',
				        'footerCredit' => 'Designed and Developed by NIC &copy; 2026',
				        'footerStyle' => 'home-strip',
				        'footerNote' => 'For CAMS portal support, DGA page updates, and technical assistance.',
				        'footerMail' => 'cams.dga@tn.gov.in',
				    ])
		    <script>
		        (function () {
			            var topbar = document.querySelector('.dga-topbar');
			            var header = document.querySelector('.dga-header');
			            var languageButtons = document.querySelectorAll('[data-dga-language]');
			            var themeButtons = document.querySelectorAll('[data-dga-theme]');
			            var fontButtons = document.querySelectorAll('[data-dga-font]');
		            var departmentSectionLinks = document.querySelectorAll('.dept-side .dga-hero-menu-button');
		            var topScrollLinks = document.querySelectorAll('[data-dga-scroll-top]');
		            var sectionScrollAreas = [
		                window,
		                document.querySelector('.dept-page'),
		                document.querySelector('.dept-content')
		            ].filter(Boolean);
		            var fontSizes = [14, 16, 18, 20];
            var defaultFontSize = 16;
            var currentFontSize = Number(localStorage.getItem('dgaFontSize') || defaultFontSize);

            function setActive(buttons, attr, value) {
                buttons.forEach(function (button) {
                    var active = button.getAttribute(attr) === value;
                    button.classList.toggle('is-active', active);
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            }

	            function applyTheme(theme) {
	                var selectedTheme = ['blue', 'green', 'gold'].indexOf(theme) !== -1 ? theme : 'blue';
	                document.body.classList.remove('dga-theme-green', 'dga-theme-gold');

	                if (selectedTheme === 'green') {
	                    document.body.classList.add('dga-theme-green');
	                } else if (selectedTheme === 'gold') {
	                    document.body.classList.add('dga-theme-gold');
	                }

	                localStorage.setItem('dgaTheme', selectedTheme);
	                setActive(themeButtons, 'data-dga-theme', selectedTheme);
	            }

            function applyLanguage(language) {
                var selectedLanguage = language === 'ta' ? 'ta' : 'en';
                localStorage.setItem('dgaLanguage', selectedLanguage);
                setActive(languageButtons, 'data-dga-language', selectedLanguage);
            }

	            function applyFontSize(size) {
	                var nextSize = fontSizes.includes(size) ? size : defaultFontSize;
	                currentFontSize = nextSize;
                document.documentElement.style.fontSize = nextSize + 'px';
                localStorage.setItem('dgaFontSize', nextSize);
                fontButtons.forEach(function (button) {
                    var action = button.getAttribute('data-dga-font');
                    var active = (nextSize === defaultFontSize && action === 'reset')
                        || (nextSize < defaultFontSize && action === 'decrease')
                        || (nextSize > defaultFontSize && action === 'increase');
	                    button.classList.toggle('is-active', active);
	                });
	            }

		            function setDepartmentChromeHeight() {
		                var fixedHeight = 0;
		                var topbarHeight = 0;

		                if (topbar) {
		                    topbarHeight = topbar.offsetHeight;
		                    fixedHeight += topbarHeight;
		                }

		                if (header) {
		                    fixedHeight += header.offsetHeight;
		                }

                document.documentElement.style.setProperty('--dga-topbar-height', topbarHeight + 'px');
                document.documentElement.style.setProperty('--dga-fixed-top-height', fixedHeight + 'px');
                document.documentElement.style.setProperty('--dga-sticky-nav-top', '12px');
		            }

            languageButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyLanguage(button.getAttribute('data-dga-language'));
                });
            });

            themeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyTheme(button.getAttribute('data-dga-theme'));
                });
            });

            fontButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var action = button.getAttribute('data-dga-font');
                    var index = fontSizes.indexOf(currentFontSize);
	                    if (action === 'decrease') {
	                        applyFontSize(fontSizes[Math.max(0, index - 1)]);
	                    } else if (action === 'increase') {
	                        applyFontSize(fontSizes[Math.min(fontSizes.length - 1, index + 1)]);
	                    } else {
	                        applyFontSize(defaultFontSize);
	                    }
	                });
	            });

		            var departmentSections = Array.from(departmentSectionLinks)
		                .map(function (link) {
		                    var target = document.querySelector(link.getAttribute('href'));

		                    return target ? { link: link, target: target } : null;
		                })
		                .filter(Boolean);

		            function getDepartmentScrollOffset() {
			                var topbarHeight = topbar ? topbar.getBoundingClientRect().height : 0;
			                var headerHeight = header ? header.getBoundingClientRect().height : 0;

			                return topbarHeight + headerHeight + 18;
			            }

		            function setActiveDepartmentSection(activeLink) {
		                departmentSectionLinks.forEach(function (item) {
		                    var active = item === activeLink;

		                    item.classList.toggle('is-active', active);
		                    item.setAttribute('aria-current', active ? 'true' : 'false');
		                });
		            }

		            var pendingDepartmentSectionFrame = null;
		            var clickedDepartmentLink = null;
		            var clickedDepartmentLockUntil = 0;

		            function getDepartmentSectionScrollTarget(section) {
		                return section.target;
		            }

		            function scrollToDepartmentSection(section) {
		                var target = getDepartmentSectionScrollTarget(section);
		                var scrollMargin = getDepartmentScrollOffset() + 'px';

		                target.style.scrollMarginTop = scrollMargin;

		                if (typeof target.scrollIntoView === 'function') {
		                    target.scrollIntoView({
		                        behavior: 'smooth',
		                        block: 'start',
		                        inline: 'nearest'
		                    });
		                    window.setTimeout(function () {
		                        target.style.removeProperty('scroll-margin-top');
		                    }, 1700);
		                    return;
		                }

		                var top = target.getBoundingClientRect().top + window.pageYOffset - getDepartmentScrollOffset();
		                document.documentElement.scrollTop = Math.max(0, top);
		                document.body.scrollTop = Math.max(0, top);
		            }

		            function updateActiveDepartmentSection() {
		                if (!departmentSections.length) {
		                    return;
		                }

		                if (clickedDepartmentLink && Date.now() < clickedDepartmentLockUntil) {
		                    setActiveDepartmentSection(clickedDepartmentLink);
		                    return;
		                }

		                var offset = getDepartmentScrollOffset();
		                var readLine = offset + Math.min(window.innerHeight * .34, 260);
		                var activeSection = departmentSections[0];
		                var closestDistance = Number.POSITIVE_INFINITY;
		                var visibleSections = [];

		                departmentSections.forEach(function (section) {
		                    var rect = section.target.getBoundingClientRect();

		                    if (rect.top <= readLine && rect.bottom > readLine) {
		                        visibleSections.push({
		                            section: section,
		                            top: rect.top
		                        });
		                        return;
		                    }

		                    if (rect.top <= readLine) {
		                        var distance = Math.abs(readLine - rect.top);

		                        if (distance < closestDistance) {
		                            closestDistance = distance;
		                            activeSection = section;
		                        }
		                    }
		                });

		                if (visibleSections.length) {
		                    activeSection = visibleSections.reduce(function (selected, current) {
		                        var sameRow = Math.abs(current.top - selected.top) <= 12;

		                        if (sameRow) {
		                            return selected;
		                        }

		                        return current.top > selected.top ? current : selected;
		                    }).section;
		                }

		                setActiveDepartmentSection(activeSection.link);
		            }

			            function requestDepartmentSectionUpdate() {
			                if (pendingDepartmentSectionFrame) {
			                    return;
			                }

			                pendingDepartmentSectionFrame = window.requestAnimationFrame(function () {
			                    pendingDepartmentSectionFrame = null;
			                    updateActiveDepartmentSection();
			                });
			            }

			            function scrollDepartmentPageToTop() {
			                sectionScrollAreas.forEach(function (area) {
			                    if (area === window) {
			                        if (typeof window.scrollTo === 'function') {
			                            window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
			                        } else {
			                            document.documentElement.scrollTop = 0;
			                            document.body.scrollTop = 0;
			                        }
			                        return;
			                    }

			                    if (area && typeof area.scrollTo === 'function') {
			                        area.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
			                    } else if (area) {
			                        area.scrollTop = 0;
			                        area.scrollLeft = 0;
			                    }
			                });

			                document.documentElement.scrollTop = 0;
			                document.body.scrollTop = 0;
			            }

			            departmentSections.forEach(function (section) {
			                section.link.addEventListener('click', function (event) {
			                    event.preventDefault();
			                    clickedDepartmentLink = section.link;
		                    clickedDepartmentLockUntil = Date.now() + 1500;
		                    setActiveDepartmentSection(section.link);
		                    window.history.replaceState(null, '', section.link.getAttribute('href'));
		                    scrollToDepartmentSection(section);
		                    window.setTimeout(function () {
		                        setActiveDepartmentSection(section.link);
		                    }, 520);
		                });
		            });

		            topScrollLinks.forEach(function (link) {
		                link.addEventListener('click', function (event) {
		                    event.preventDefault();
			                    clickedDepartmentLink = null;
			                    clickedDepartmentLockUntil = 0;
			                    setActiveDepartmentSection(departmentSections[0] ? departmentSections[0].link : null);
			                    window.history.replaceState(null, '', '#dga-page-top');
			                    scrollDepartmentPageToTop();

			                    window.setTimeout(updateActiveDepartmentSection, 420);
			                });
			            });

			            setDepartmentChromeHeight();

			            if (departmentSections.length) {
			                updateActiveDepartmentSection();
			                sectionScrollAreas.forEach(function (area) {
			                    area.addEventListener('scroll', requestDepartmentSectionUpdate, { passive: true });
		                });
			                document.addEventListener('scroll', requestDepartmentSectionUpdate, true);
			                window.addEventListener('hashchange', requestDepartmentSectionUpdate);
			                window.addEventListener('resize', function () {
			                    setDepartmentChromeHeight();
			                    requestDepartmentSectionUpdate();
			                });
			            }

			            window.addEventListener('load', setDepartmentChromeHeight);
		            window.addEventListener('resize', setDepartmentChromeHeight);
		            applyLanguage(localStorage.getItem('dgaLanguage') || 'en');
		            applyTheme(localStorage.getItem('dgaTheme') || 'blue');
		            applyFontSize(currentFontSize);
	        })();
    </script>
    <script src="{{ asset('site/js/dga-responsive.js') }}?v=20260724e"></script>
</body>

</html>
