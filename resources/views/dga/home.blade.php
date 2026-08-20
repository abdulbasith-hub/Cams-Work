<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DGA - CAMS</title>
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
            --dga-theme-secondary: #25756b;
            --dga-theme-light: #62a9df;
            --dga-theme-accent: #f0b84f;
            --dga-theme-soft: #eaf6ff;
            --dga-theme-soft-2: #edf9f5;
            --dga-theme-hover: #b7dcf4;
            --dga-theme-shadow: rgba(47, 117, 181, .24);
        }

	        body {
	            margin: 0;
	            background: var(--dga-bg);
	            color: var(--dga-ink);
	            font-family: Arial, Helvetica, sans-serif;
	            overflow-x: hidden;
	        }

        .dga-topbar {
            background: #063b67;
            color: #fff;
            font-size: 14px;
        }

        .dga-topbar .container {
            gap: 12px;
        }

        .dga-topbar-tools {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

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

        .dga-language-switch button,
        .dga-theme-switch button {
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: rgba(255, 255, 255, .84);
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .dga-language-switch button {
            min-height: 26px;
            padding: 6px 11px;
        }

        .dga-theme-switch button {
            width: 26px;
            height: 26px;
            padding: 0;
        }

        .dga-language-switch button:hover,
        .dga-language-switch button:focus,
        .dga-theme-switch button:hover,
        .dga-theme-switch button:focus,
        .dga-language-switch button.is-active,
        .dga-theme-switch button.is-active {
            background: #ffffff;
            color: #174f82;
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

        .dga-home-body.dga-theme-green {
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
            --dga-theme-secondary: #45a98d;
            --dga-theme-light: #78c8ae;
            --dga-theme-accent: #d9a338;
            --dga-theme-soft: #e7f7f2;
            --dga-theme-soft-2: #edf9f5;
            --dga-theme-hover: #bae7d9;
            --dga-theme-shadow: rgba(33, 121, 108, .24);
        }

        .dga-home-body.dga-theme-gold {
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
            --dga-theme-secondary: #21796c;
            --dga-theme-light: #f0c05a;
            --dga-theme-accent: #45a98d;
            --dga-theme-soft: #fff6dc;
            --dga-theme-soft-2: #edf9f5;
            --dga-theme-hover: #f8e6b7;
            --dga-theme-shadow: rgba(169, 119, 22, .24);
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
            color: #435268;
            font-size: 14px;
        }

        .dga-brand-accessibility {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 7px;
        }

        .dga-topbar .dga-brand-accessibility {
            margin-top: 0;
        }

        .dga-screen-reader-link,
        .dga-screen-reader-link:hover,
        .dga-screen-reader-link:focus {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 30px;
            padding: 6px 11px;
            border: 1px solid #c8e0ef;
            border-radius: 999px;
            background: #f6fbff;
            color: #174f82;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 8px 18px rgba(32, 54, 74, .08);
        }

        .dga-font-controls {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px;
            border: 1px solid #c8e0ef;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .85), 0 8px 18px rgba(32, 54, 74, .07);
        }

        .dga-font-controls button {
            min-width: 30px;
            height: 26px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #174f82;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .dga-font-controls button:hover,
        .dga-font-controls button:focus,
        .dga-font-controls button.is-active {
            background: #174f82;
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(23, 79, 130, .18);
            transform: translateY(-1px);
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
            border: 1px solid #c8d5e4;
            color: var(--dga-blue);
            background: #fff;
            font-weight: 700;
            text-decoration: none;
        }

        .dga-nav-link.primary,
        .dga-nav-link.primary:hover {
            border-color: var(--dga-blue);
            background: var(--dga-blue);
            color: #fff;
        }

        .dga-department-strip {
            position: relative;
            background: linear-gradient(90deg, #0b3151 0%, #0f496b 48%, #117584 100%);
            box-shadow: 0 10px 22px rgba(14, 28, 45, .16);
        }

        .dga-department-strip::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2px;
            background: linear-gradient(90deg, rgba(225, 177, 75, .9), rgba(134, 221, 229, .95), rgba(225, 177, 75, .9));
        }

        .dga-department-grid {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 10px;
            padding: 10px 0;
            background: transparent;
        }

        .dga-department-item {
            position: relative;
            flex: 0 0 auto;
        }

        .dga-department-title,
        .dga-department-title:hover,
        .dga-department-title:focus {
            position: relative;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 12px;
            background: rgba(255, 255, 255, .02);
            color: #fff;
            text-decoration: none;
            transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
        }

        .dga-department-title::before {
            content: "";
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 7px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, #e1b14b, #8cdee6);
            transform: scaleX(0);
            transform-origin: center;
            transition: transform .22s ease;
        }

        .dga-department-title:hover,
        .dga-department-title:focus,
        .dga-department-item.is-open .dga-department-title {
            background: rgba(255, 255, 255, .08);
            border-color: rgba(255, 255, 255, .16);
            color: #fff;
        }

        .dga-department-title:hover::before,
        .dga-department-title:focus::before,
        .dga-department-item.is-open .dga-department-title::before {
            transform: scaleX(1);
        }

        .dga-department-title:focus {
            outline: 2px solid rgba(140, 222, 230, .75);
            outline-offset: 2px;
        }

        .dga-department-title strong {
            color: inherit;
            font-size: 15px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: .01em;
        }

        .dga-department-arrow {
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            transition: transform .2s ease, color .2s ease;
        }

        .dga-department-item:hover .dga-department-arrow,
        .dga-department-item:focus-within .dga-department-arrow,
        .dga-department-item.is-open .dga-department-arrow {
            color: #9fe7ed;
            transform: translateY(1px);
        }

        .dga-department-menu {
            position: absolute;
            left: 0;
            top: calc(100% + 10px);
            display: none;
            min-width: 248px;
            padding: 10px;
            border: 1px solid rgba(188, 205, 218, .9);
            border-radius: 14px;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 34px rgba(17, 34, 49, .16);
            backdrop-filter: blur(12px);
            z-index: 45;
        }

        .dga-department-item:hover .dga-department-menu,
        .dga-department-item:focus-within .dga-department-menu,
        .dga-department-item.is-open .dga-department-menu {
            display: block;
        }

        .dga-department-menu a {
            display: block;
            padding: 10px 12px;
            border-radius: 10px;
            color: #203349;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .dga-department-menu a:hover,
        .dga-department-menu a:focus {
            background: linear-gradient(90deg, rgba(11, 49, 81, .08), rgba(17, 117, 132, .12));
            color: #0b3151;
            text-decoration: none;
            transform: translateX(2px);
        }

        .dga-hero {
            position: relative;
            min-height: 560px;
            display: flex;
            align-items: center;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(4, 38, 67, .96), rgba(8, 88, 132, .84), rgba(4, 54, 64, .68)),
                url("{{ asset('site/image/bg1-min.png') }}") center/cover no-repeat;
        }

        .dga-hero .container {
            position: relative;
        }

        .dga-page-sections-band {
            padding: 18px 0;
            background: #eef4f8;
            border-bottom: 1px solid var(--dga-line);
        }

        .dga-hero-layout {
            width: min(100%, 1180px);
            min-height: 560px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin: 0 auto;
        }

        .dga-updates-section {
            padding: 46px 0;
            background: #fff;
        }

        .dga-updates-panel {
            width: min(100%, 780px);
            min-height: 330px;
            margin: 0 auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .3);
            background:
                linear-gradient(180deg, rgba(5, 34, 60, .96), rgba(4, 74, 89, .94)),
                radial-gradient(circle at top left, rgba(243, 192, 51, .2), transparent 38%);
            box-shadow: 0 24px 58px rgba(22, 32, 42, .18);
        }

        .dga-updates-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 18px;
            background: rgba(0, 0, 0, .18);
            color: #fff;
            font-size: 18px;
            font-weight: 800;
        }

        .dga-updates-head::after {
            content: "NEW";
            padding: 3px 8px;
            border-radius: 4px;
            background: #f3c033;
            color: #16202a;
            font-size: 11px;
            line-height: 1;
        }

        .dga-updates-window {
            position: relative;
            flex: 1;
            min-height: 0;
            height: 274px;
            overflow: hidden;
        }

        .dga-updates-track {
            display: grid;
            gap: 0;
            padding: 12px 0;
            animation: dgaUpdatesScroll 22s linear infinite;
        }

        .dga-updates-window:hover .dga-updates-track,
        .dga-updates-window:focus-within .dga-updates-track {
            animation-play-state: paused;
        }

        .dga-update-item {
            min-height: 104px;
            margin: 0 12px 10px;
            padding: 13px 14px 14px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-left: 4px solid #f3c033;
            border-radius: 8px;
            background: rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .92);
            line-height: 1.45;
        }

        .dga-update-item:nth-child(2n) {
            background: rgba(255, 255, 255, .16);
        }

        .dga-update-item strong {
            display: inline-block;
            margin-bottom: 6px;
            padding: 2px 7px;
            border-radius: 4px;
            background: #f3c033;
            color: #172438;
            font-size: 13px;
        }

        .dga-hero-copy {
            position: static;
            flex: 0 1 1180px;
            max-width: 1180px;
            margin: 0;
            padding: 10px 22px;
            text-align: left;
        }

        @keyframes dgaUpdatesScroll {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-50%);
            }
        }

        .dga-hero h2 {
            color: #fff;
            max-width: 980px;
            margin: 0 0 18px;
            font-size: 48px;
            font-weight: 800;
            line-height: 1.08;
            text-align: left;
            text-shadow: 0 12px 30px rgba(0, 0, 0, .34);
            margin-left: 0;
            margin-right: 0;
        }

        .dga-hero p {
            color: rgba(255, 255, 255, .92);
            font-size: 18px;
            line-height: 1.7;
            max-width: 900px;
            margin-right: 0;
            margin-left: 0;
        }

        .dga-eyebrow {
            display: inline-block;
            margin-bottom: 16px;
            padding: 8px 14px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, .36);
            background: rgba(255, 255, 255, .11);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .dga-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 30px;
        }

        .dga-hero-section-grid {
            position: relative;
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 1066px;
            margin: 0;
            align-items: start;
        }

        .dga-hero-menu-card {
            padding: 18px 18px 24px;
            border-top: 5px solid #1265a8;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 16px 36px rgba(22, 32, 42, .16);
            text-align: left;
        }

        .dga-hero-menu-title {
            margin: 0 0 12px;
            color: #172231;
            font-size: 19px;
            font-weight: 800;
        }

        .dga-hero-menu {
            display: grid;
            grid-template-columns: 1fr;
            gap: 7px;
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .dga-hero-menu-button {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 10px 12px;
            border: 1px solid rgba(47, 117, 181, .24);
            border-radius: 4px;
            background: #f3f6fa;
            color: #243044;
            cursor: pointer;
            font-weight: 800;
            text-align: left;
            transition: transform .18s ease, background .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .dga-hero-menu-button i {
            display: none;
        }

        .dga-hero-menu-button:hover,
        .dga-hero-menu-button:focus,
        .dga-hero-menu-button.is-active {
            transform: translateX(4px);
            background: #1265a8;
            color: #fff;
            box-shadow: 0 10px 22px rgba(18, 101, 168, .22);
            outline: none;
        }

        .dga-hero-menu-button.is-active i {
            display: none;
        }

        .dga-hero-content-shell {
            position: relative;
            display: none;
            min-height: 308px;
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 22px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 8px;
            background: rgba(4, 24, 42, .58);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14), 0 24px 54px rgba(0, 0, 0, .22);
            text-align: left;
        }

        .dga-hero-content-shell.is-open {
            display: block;
            animation: dgaPanelSlide .46s ease both;
        }

        .dga-hero-panel {
            display: none;
        }

        .dga-hero-panel.is-active {
            display: block;
            animation: dgaPanelSlide .46s ease both;
        }

        @keyframes dgaPanelSlide {
            from {
                opacity: 0;
                transform: translateX(72px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .dga-hero-panel h3 {
            margin: 0 0 14px;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
        }

        .dga-hero-panel .dga-stat-grid {
            margin: 0 0 16px;
        }

        .dga-hero-card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .dga-hero-card-grid.compact {
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        }

        .dga-hero-info-card,
        .dga-hero-mini-step {
            min-height: 100%;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 8px;
            background: rgba(255, 255, 255, .11);
            color: rgba(255, 255, 255, .92);
        }

        .dga-hero-info-card h4,
        .dga-hero-mini-step h4 {
            margin: 0 0 10px;
            color: #f3c033;
            font-size: 17px;
            font-weight: 800;
        }

        .dga-hero-info-card p,
        .dga-hero-mini-step p {
            max-width: none;
            margin: 0;
            color: rgba(255, 255, 255, .88);
            font-size: 14px;
            line-height: 1.55;
        }

        .dga-hero-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dga-hero-list li {
            position: relative;
            margin-bottom: 10px;
            padding-left: 22px;
            color: rgba(255, 255, 255, .9);
            line-height: 1.55;
        }

        .dga-hero-list li:last-child {
            margin-bottom: 0;
        }

        .dga-hero-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 9px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #f3c033;
        }

        .dga-hero-flow-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding-top: 30px;
            margin-top: 12px;
        }

        .dga-hero-flow-grid::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 28px;
            background: linear-gradient(180deg, rgba(243, 192, 51, .95), rgba(243, 192, 51, 0));
        }

        .dga-hero-flow-root {
            margin: 0 auto 28px;
            width: min(100%, 460px);
            padding: 18px 24px;
            border-radius: 999px;
            background: radial-gradient(circle at top left, #fff8d1 0%, #f3c033 35%, #dda516 100%);
            color: #16202a;
            font-size: 20px;
            font-weight: 900;
            text-align: center;
            box-shadow: 0 20px 40px rgba(23, 40, 60, .18);
            position: relative;
        }

        .dga-hero-flow-root::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -14px;
            transform: translateX(-50%);
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #f3c033;
            box-shadow: 0 0 0 6px rgba(243, 192, 51, .18);
        }

        .dga-hero-flow-grid .dga-hero-info-card {
            position: relative;
            overflow: hidden;
            padding: 22px 20px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            box-shadow: 0 22px 54px rgba(8, 20, 38, .18);
            backdrop-filter: blur(12px);
        }

        .dga-hero-flow-grid .dga-hero-info-card::before {
            content: "";
            position: absolute;
            top: 24px;
            left: -18px;
            width: 22px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(243, 192, 51, .95), rgba(243, 192, 51, 0));
        }

        .dga-hero-flow-grid .dga-hero-info-card::after {
            content: "";
            position: absolute;
            top: 20px;
            left: -9px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #f3c033;
            box-shadow: 0 0 0 4px rgba(243, 192, 51, .14);
        }

        .dga-hero-flow-grid .dga-hero-info-card h4 {
            color: #fffdea;
        }

        .dga-hero-roadmap-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
            counter-reset: heroRoadmap;
        }

        .dga-hero-roadmap-list li {
            position: relative;
            min-height: 110px;
            padding: 14px 14px 14px 52px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .11);
            color: rgba(255, 255, 255, .9);
            line-height: 1.45;
        }

        .dga-hero-roadmap-list li::before {
            counter-increment: heroRoadmap;
            content: counter(heroRoadmap);
            position: absolute;
            left: 14px;
            top: 15px;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f3c033;
            color: #16202a;
            font-size: 13px;
            font-weight: 800;
        }

        .dga-hero-roadmap-list strong {
            display: block;
            color: #fff;
            margin-bottom: 5px;
        }

        .dga-hero-process-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .dga-hero-mini-step {
            position: relative;
            min-height: 186px;
            padding: 78px 18px 20px;
            overflow: hidden;
        }

        .dga-hero-mini-step::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, #0f4f78, #2f8b97);
        }

        .dga-hero-mini-step::after {
            content: "";
            position: absolute;
            right: -38px;
            top: -34px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47, 139, 151, .16), rgba(47, 139, 151, 0));
        }

        .dga-hero-mini-step i {
            position: absolute;
            left: 18px;
            top: 18px;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #153959, #2f8b97);
            color: #fff;
            box-shadow: 0 14px 24px rgba(21, 57, 89, .2);
        }

        .dga-hero-process-tag {
            position: absolute;
            top: 20px;
            right: 18px;
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(21, 57, 89, .08);
            color: #153959;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-hero-mini-step:nth-child(2n)::before {
            background: linear-gradient(180deg, #c07a21, #e0a037);
        }

        .dga-hero-mini-step:nth-child(2n)::after {
            background: radial-gradient(circle, rgba(224, 160, 55, .18), rgba(224, 160, 55, 0));
        }

        .dga-hero-mini-step:nth-child(2n) i {
            background: linear-gradient(135deg, #b7741e, #e0a037);
            box-shadow: 0 14px 24px rgba(183, 116, 30, .22);
        }

        .dga-hero-mini-step:nth-child(3n)::before {
            background: linear-gradient(180deg, #1f6a53, #55a86f);
        }

        .dga-hero-mini-step:nth-child(3n)::after {
            background: radial-gradient(circle, rgba(85, 168, 111, .18), rgba(85, 168, 111, 0));
        }

        .dga-hero-mini-step:nth-child(3n) i {
            background: linear-gradient(135deg, #1f6a53, #55a86f);
            box-shadow: 0 14px 24px rgba(31, 106, 83, .22);
        }

        .dga-stat {
            min-height: 118px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .14);
            color: #fff;
            text-align: center;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .13);
        }

        .dga-stat strong {
            display: block;
            margin-bottom: 8px;
            font-size: 24px;
        }

        .dga-section {
            padding: 60px 0;
        }

        .dga-section-title {
            margin-bottom: 28px;
        }

        .dga-section-title span {
            color: var(--dga-teal);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .dga-section-title h3 {
            margin: 8px 0 0;
            color: var(--dga-ink);
            font-size: 30px;
            font-weight: 800;
        }

        .dga-panel {
            height: 100%;
            padding: 24px;
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 34px rgba(22, 32, 42, .06);
        }

        .dga-panel h4 {
            color: var(--dga-blue);
            font-weight: 800;
            font-size: 20px;
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
            background: var(--dga-green);
        }

        .dga-overview-stack {
            display: grid;
            gap: 42px 30px;
        }

        .dga-overview-band {
            display: grid;
            grid-template-columns: 190px minmax(0, 1fr);
            min-height: 0;
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 12px 34px rgba(22, 32, 42, .06);
        }

        .dga-overview-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px;
            background: linear-gradient(180deg, #063b67, #0b5fa5);
            color: #fff;
            text-align: center;
        }

        .dga-overview-band:nth-child(2) .dga-overview-label {
            background: linear-gradient(180deg, #027c86, #2d7b4f);
        }

        .dga-overview-label h4 {
            margin: 0;
            color: inherit;
            font-size: 24px;
            font-weight: 800;
        }

        .dga-overview-content {
            padding: 18px;
        }

        .dga-overview-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
            margin: 0;
            padding: 0;
            list-style: none;
            counter-reset: overviewPoint;
        }

        .dga-overview-list li {
            position: relative;
            min-height: 118px;
            padding: 14px 14px 14px 52px;
            border-left: 4px solid #f3c033;
            background: #f7faff;
            color: #233246;
            line-height: 1.5;
        }

        .dga-overview-list li::before {
            counter-increment: overviewPoint;
            content: counter(overviewPoint);
            position: absolute;
            left: 14px;
            top: 16px;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #063b67;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }

        .dga-flow-map {
            overflow-x: auto;
            padding: 12px 0 18px;
        }

        .dga-org-chart {
            position: relative;
            min-width: 820px;
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px;
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(241, 247, 255, .96)),
                radial-gradient(circle at top right, rgba(73, 105, 238, .12), transparent 34%);
            box-shadow: 0 22px 54px rgba(22, 32, 42, .1);
            opacity: 0;
            transform: translateX(-70px);
            transition: opacity .75s ease, transform .75s ease;
        }

        .dga-flow-map.is-visible .dga-org-chart {
            opacity: 1;
            transform: translateX(0);
        }

        .dga-flow-node {
            position: relative;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 11px 14px;
            border-radius: 8px;
            color: var(--dga-ink);
            font-weight: 800;
            line-height: 1.25;
            text-align: center;
            border: 1px solid rgba(216, 226, 239, .9);
            background: #fff;
            box-shadow: 0 12px 26px rgba(22, 32, 42, .07);
        }

        .dga-flow-top {
            width: min(100%, 380px);
            min-height: 66px;
            margin: 0 auto;
            border-top: 1px solid rgba(47, 117, 181, .24);
            border-left: 1px solid rgba(47, 117, 181, .24);
            border-radius: 8px;
            background: linear-gradient(135deg, #063b67, #0b5fa5);
            color: #fff;
            font-size: 24px;
            box-shadow: 0 18px 36px rgba(6, 59, 103, .28);
        }

        .dga-flow-top::after {
            display: none;
        }

        .dga-org-main {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
            margin-top: 24px;
        }

        .dga-org-main::before {
            display: none;
        }

        .dga-org-column {
            position: relative;
            padding: 18px;
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background: rgba(255, 255, 255, .78);
        }

        .dga-org-column::before {
            display: none;
        }

        .dga-flow-blue {
            min-height: 60px;
            border: 0;
            background: linear-gradient(135deg, #4969ee, #0b5fa5);
            color: #fff;
            font-size: 18px;
            box-shadow: 0 14px 28px rgba(73, 105, 238, .22);
        }

        .dga-flow-magenta {
            border-left: 5px solid #b77708;
            background: #fff8ec;
            color: #4b3412;
            font-size: 16px;
        }

        .dga-flow-green {
            border-left: 5px solid #2d7b4f;
            background: #f3fbf6;
            color: #173b29;
            font-size: 15px;
            font-weight: 700;
        }

        .dga-org-subgrid {
            position: relative;
            display: grid;
            grid-template-columns: minmax(150px, .9fr) minmax(210px, 1.1fr);
            gap: 14px;
            margin-top: 16px;
        }

        .dga-org-subgrid::before {
            display: none;
        }

        .dga-org-subcolumn {
            position: relative;
        }

        .dga-org-subcolumn::before {
            display: none;
        }

        .dga-org-stack {
            position: relative;
            display: grid;
            gap: 12px;
        }

        .dga-org-stack::before {
            display: none;
        }

        .dga-org-stack .dga-flow-node::before,
        .dga-org-directorates .dga-flow-node::before {
            display: none;
        }

        .dga-org-ps-stack {
            padding-left: 0;
        }

        .dga-org-ps-stack .dga-flow-node::before {
            display: none;
        }

        .dga-org-directorates {
            position: relative;
            display: grid;
            gap: 12px;
            margin-top: 16px;
            padding-left: 0;
        }

        .dga-org-directorates::before {
            display: none;
        }

        .dga-org-directorates .dga-flow-node::before {
            display: none;
        }

        .dga-org-directorates .dga-flow-node {
            border-left-color: #8a5b05;
            background: #fff7fb;
            color: #4a153e;
        }

        .dga-directorate-link {
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
        }

        .dga-directorate-link:hover,
        .dga-directorate-link:focus {
            transform: translateX(8px) scale(1.03);
            background: linear-gradient(135deg, #8a5b05, #b77708);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 18px 36px rgba(138, 91, 5, .24);
            outline: 2px solid rgba(183, 119, 8, .35);
            outline-offset: 3px;
        }

        .dga-wing-grid,
        .dga-module-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 62px 58px;
        }

        .dga-wing {
            border-left: 5px solid var(--dga-teal);
        }

        .dga-wing:nth-child(2n) {
            border-left-color: var(--dga-gold);
        }

        .dga-function-roadmap-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 28px;
            align-items: start;
        }

        .dga-function-roadmap-col {
            min-width: 0;
        }

        .dga-function-roadmap-col .dga-section-title {
            min-height: 88px;
            margin-bottom: 18px;
        }

        .dga-function-roadmap-col .dga-wing-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .dga-function-roadmap-col .dga-wing {
            padding: 18px;
        }

        .dga-function-roadmap-col .dga-wing h4 {
            font-size: 18px;
        }

        .dga-function-roadmap-col .dga-roadmap {
            max-width: none;
            padding: 0 0 0 58px;
        }

        .dga-function-roadmap-col .dga-roadmap::before {
            left: 20px;
            transform: none;
        }

        .dga-function-roadmap-col .dga-roadmap-item,
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(odd),
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(even) {
            width: 100%;
            min-height: 0;
            justify-self: stretch;
            margin-bottom: 14px;
            padding: 16px 18px;
        }

        .dga-function-roadmap-col .dga-roadmap-item::before,
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(odd)::before,
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(even)::before {
            left: -38px;
            right: auto;
            width: 38px;
        }

        .dga-function-roadmap-col .dga-roadmap-item small,
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(odd) small,
        .dga-function-roadmap-col .dga-roadmap-item:nth-child(even) small {
            left: -58px;
            right: auto;
        }

        .dga-process-flow {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            padding: 14px 0 0;
        }

        .dga-process-step {
            position: relative;
            min-height: 220px;
            padding: 78px 18px 20px;
            border: 1px solid rgba(216, 226, 239, .92);
            border-radius: 22px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(245, 249, 255, .98) 62%, rgba(238, 247, 244, .98)),
                radial-gradient(circle at top right, rgba(47, 139, 151, .14), transparent 42%);
            box-shadow: 0 18px 42px rgba(22, 32, 42, .11);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            overflow: hidden;
        }

        .dga-process-step::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 6px;
            background: linear-gradient(180deg, #0f4f78, #2f8b97);
        }

        .dga-process-step::after {
            content: "";
            position: absolute;
            right: -36px;
            top: -34px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47, 139, 151, .15), rgba(47, 139, 151, 0));
        }

        .dga-process-step:hover {
            transform: translateY(-6px);
            border-color: rgba(47, 139, 151, .38);
            box-shadow: 0 26px 58px rgba(22, 32, 42, .17);
        }

        .dga-process-no {
            position: absolute;
            right: 18px;
            top: 18px;
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(21, 57, 89, .08);
            color: #153959;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-process-icon {
            position: absolute;
            left: 18px;
            top: 18px;
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #153959, #2f8b97);
            color: #fff;
            font-size: 22px;
            box-shadow: 0 14px 26px rgba(21, 57, 89, .22);
        }

        .dga-process-step h4 {
            margin: 0 0 10px;
            color: var(--dga-ink);
            font-size: 18px;
            font-weight: 800;
        }

        .dga-process-step p {
            margin: 0;
            color: #435268;
            line-height: 1.6;
        }

        .dga-process-step:nth-child(2n)::before {
            background: linear-gradient(180deg, #b7741e, #e0a037);
        }

        .dga-process-step:nth-child(2n)::after {
            background: radial-gradient(circle, rgba(224, 160, 55, .16), rgba(224, 160, 55, 0));
        }

        .dga-process-step:nth-child(2n) .dga-process-icon {
            background: linear-gradient(135deg, #b7741e, #e0a037);
            box-shadow: 0 14px 26px rgba(183, 116, 30, .22);
        }

        .dga-process-step:nth-child(3n)::before {
            background: linear-gradient(180deg, #1f6a53, #55a86f);
        }

        .dga-process-step:nth-child(3n)::after {
            background: radial-gradient(circle, rgba(85, 168, 111, .16), rgba(85, 168, 111, 0));
        }

        .dga-process-step:nth-child(3n) .dga-process-icon {
            background: linear-gradient(135deg, #1f6a53, #55a86f);
            box-shadow: 0 14px 26px rgba(31, 106, 83, .22);
        }

        .dga-roadmap {
            position: relative;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            max-width: 1120px;
            margin: 0 auto;
            padding: 10px 0;
        }

        .dga-roadmap::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: linear-gradient(180deg, #4969ee, #027c86, #b77708, #2d7b4f);
        }

        .dga-roadmap-item {
            position: relative;
            width: calc(50% - 54px);
            min-height: 170px;
            margin: 0 0 28px;
            padding: 22px 24px;
            border: 1px solid var(--dga-line);
            border-left: 6px solid #4969ee;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(247, 250, 255, .98));
            box-shadow: 0 18px 42px rgba(22, 32, 42, .1);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dga-roadmap-item:nth-child(odd) {
            justify-self: start;
        }

        .dga-roadmap-item:nth-child(even) {
            justify-self: end;
        }

        .dga-roadmap-item::before {
            content: "";
            position: absolute;
            top: 32px;
            width: 54px;
            height: 2px;
            background: #9eb2d3;
        }

        .dga-roadmap-item:nth-child(odd)::before {
            right: -54px;
        }

        .dga-roadmap-item:nth-child(even)::before {
            left: -54px;
        }

        .dga-roadmap-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 26px 58px rgba(22, 32, 42, .15);
        }

        .dga-roadmap-item:nth-child(2n) {
            border-left-color: #027c86;
        }

        .dga-roadmap-item:nth-child(3n) {
            border-left-color: #b77708;
        }

        .dga-roadmap-item:nth-child(4n) {
            border-left-color: #2d7b4f;
        }

        .dga-roadmap-item small {
            position: absolute;
            top: 12px;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            border-radius: 50%;
            background: #4969ee;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 12px 26px rgba(73, 105, 238, .32);
            z-index: 2;
        }

        .dga-roadmap-item:nth-child(odd) small {
            right: -76px;
        }

        .dga-roadmap-item:nth-child(even) small {
            left: -76px;
        }

        .dga-roadmap-item:nth-child(2n) small {
            background: #027c86;
        }

        .dga-roadmap-item:nth-child(3n) small {
            background: #b77708;
        }

        .dga-roadmap-item:nth-child(4n) small {
            background: #2d7b4f;
        }

        .dga-roadmap-item h4 {
            margin: 0 0 10px;
            color: var(--dga-ink);
            font-size: 19px;
            font-weight: 800;
        }

        .dga-roadmap-item p {
            color: #435268;
            line-height: 1.6;
        }

        .dga-module h4 {
            color: var(--dga-ink);
        }

        .dga-module ul {
            padding-left: 18px;
            margin-bottom: 0;
        }

        .dga-module li {
            margin-bottom: 8px;
        }

        .dga-footer {
            background: #082f4f;
            color: #fff;
            padding: 26px 0;
        }

        .dga-footer a {
            color: #fff;
            font-weight: 700;
        }

	        @media (max-width: 991px) {
	            .dga-topbar .container {
	                gap: 6px;
	            }

	            .dga-header .container {
	                padding-top: 10px !important;
	                padding-bottom: 10px !important;
	            }

	            .dga-brand {
	                align-items: flex-start;
	            }

	            .dga-nav {
	                justify-content: flex-start;
	            }

	            .dga-nav-link,
	            .dga-nav-link:hover {
	                min-height: 34px;
	                padding: 7px 12px;
	            }

	            .dga-department-strip {
	                overflow: visible;
	            }

	            .dga-department-grid {
	                display: grid;
	                grid-template-columns: 1fr;
	                gap: 8px;
	                padding: 10px 0 12px;
	            }

	            .dga-department-item {
	                width: 100%;
	            }

	            .dga-department-title,
	            .dga-department-title:hover,
	            .dga-department-title:focus {
	                width: 100%;
	                justify-content: space-between;
	                min-height: 44px;
	                padding: 10px 14px;
	            }

	            .dga-department-menu {
	                position: static;
	                min-width: 0;
	                width: 100%;
	                margin-top: 6px;
	            }

	            .dga-hero {
	                min-height: auto;
	                padding: 34px 0 42px;
	            }

	            .dga-hero h2 {
	                font-size: 34px;
            }

	            .dga-hero-layout {
	                min-height: 0;
	                display: grid;
		                grid-template-columns: 1fr;
		                gap: 24px;
		                padding-left: 0;
		            }

	            .dga-hero-copy {
		                padding: 0;
		            }

            .dga-hero-section-grid {
                position: relative;
                left: auto;
                top: auto;
                grid-template-columns: 1fr;
                width: min(100%, 320px);
                max-width: 320px;
                margin: 30px auto 0;
            }

            .dga-hero-menu-card {
                width: 100%;
            }

            .dga-hero-content-shell {
                position: relative;
                top: auto;
                left: auto;
                right: auto;
                max-width: 720px;
                margin: 0;
                min-height: 0;
            }

	            .dga-hero-card-grid,
	            .dga-hero-flow-grid,
	            .dga-hero-process-grid {
	                grid-template-columns: 1fr;
	            }

            .dga-hero-roadmap-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

	            .dga-updates-window {
	                height: 280px;
	            }

            .dga-overview-band {
                grid-template-columns: 1fr;
            }

            .dga-overview-label {
                justify-content: flex-start;
                text-align: left;
            }

            .dga-overview-list {
                grid-template-columns: 1fr;
            }

	            .dga-stat-grid,
	            .dga-wing-grid,
	            .dga-module-grid,
	            .dga-function-roadmap-row {
	                grid-template-columns: 1fr;
	            }

            .dga-function-roadmap-col .dga-section-title {
                min-height: 0;
            }

	            .dga-process-flow {
	                grid-template-columns: repeat(2, minmax(0, 1fr));
	            }

            .dga-org-chart {
                min-width: 760px;
            }

            .dga-org-main,
            .dga-org-subgrid {
                grid-template-columns: 1fr;
            }

            .dga-roadmap {
                max-width: 100%;
                padding-left: 54px;
            }

            .dga-roadmap::before {
                left: 18px;
            }

            .dga-roadmap-item,
            .dga-roadmap-item:nth-child(odd),
            .dga-roadmap-item:nth-child(even) {
                width: 100%;
                justify-self: stretch;
            }

            .dga-roadmap-item:nth-child(odd)::before,
            .dga-roadmap-item:nth-child(even)::before {
                left: -36px;
                right: auto;
                width: 36px;
            }

            .dga-roadmap-item:nth-child(odd) small,
            .dga-roadmap-item:nth-child(even) small {
                left: -58px;
                right: auto;
            }

            .dga-flow-row,
            .dga-flow-divisions {
                grid-template-columns: 1fr;
            }

	            .dga-section {
	                padding: 42px 0;
	            }
	        }

	        @media (max-width: 575px) {
	            .dga-topbar {
	                font-size: 12px;
	            }

	            .dga-topbar .container {
	                justify-content: center !important;
	                text-align: center;
	            }

	            .dga-header .row {
	                gap: 8px;
	            }

	            .dga-brand h1 {
	                font-size: 18px;
	            }

            .dga-brand img {
	                width: 48px;
	                height: 48px;
	            }

	            .dga-brand span {
	                font-size: 12px;
	            }

	            .dga-nav {
	                gap: 6px;
	            }

	            .dga-department-title,
	            .dga-department-title:hover,
	            .dga-department-title:focus {
	                min-height: 38px;
	                padding: 8px 12px;
	            }

	            .dga-department-title strong {
	                font-size: 14px;
	            }

	            .dga-hero {
	                padding: 28px 0 34px;
	            }

	            .dga-hero h2 {
	                font-size: 28px;
	                line-height: 1.18;
	            }

	            .dga-hero p {
	                font-size: 15px;
	                line-height: 1.6;
	            }

	            .dga-eyebrow {
	                margin-bottom: 12px;
	                padding: 7px 10px;
	                font-size: 12px;
	            }

	            .dga-stat-grid {
	                gap: 10px;
	                margin-top: 20px;
	            }

		            .dga-stat {
		                min-height: 0;
		                padding: 14px;
		            }

            .dga-hero-menu-button {
                min-height: 42px;
            }

            .dga-hero-content-shell {
                padding: 16px;
            }

            .dga-hero-roadmap-list {
                grid-template-columns: 1fr;
            }

	            .dga-section-title h3 {
	                font-size: 24px;
	            }

	            .dga-updates-window {
	                height: 240px;
            }

            .dga-overview-content {
                padding: 16px;
            }

	            .dga-overview-label h4 {
	                font-size: 20px;
	            }

	            .dga-overview-list li {
	                min-height: 0;
	            }

	            .dga-panel {
	                padding: 18px;
	            }

	            .dga-flow-map,
	            .dga-process-flow {
	                margin-right: -12px;
	                padding-right: 12px;
	            }

	            .dga-process-flow {
	                grid-template-columns: 1fr;
	                margin-right: 0;
	                padding-right: 0;
	            }

	            .dga-org-chart {
	                min-width: 680px;
	                padding: 16px;
	            }

	            .dga-roadmap {
	                padding-left: 46px;
	            }

	            .dga-roadmap-item,
	            .dga-roadmap-item:nth-child(odd),
	            .dga-roadmap-item:nth-child(even) {
	                padding: 16px;
	            }

	            .dga-roadmap-item:nth-child(odd) small,
	            .dga-roadmap-item:nth-child(even) small {
	                left: -52px;
	            }

	            .dga-footer .container {
	                gap: 8px;
	                justify-content: center !important;
	                text-align: center;
	            }
	        }

        .dga-home-body .dga-department-strip {
            border-top: 1px solid color-mix(in srgb, var(--dga-theme-light) 20%, transparent);
            border-bottom: 1px solid color-mix(in srgb, var(--dga-theme-dark) 36%, transparent);
            box-shadow: 0 8px 18px rgba(13, 66, 96, .16);
        }

        .dga-home-body .dga-department-strip::before {
            display: none;
        }

        .dga-home-body .dga-department-strip::after {
            height: 1px;
            opacity: .65;
        }

        .dga-home-body .dga-department-strip .container {
            max-width: none;
                        padding-top: clamp(18px, 3vw, 54px);

            padding-left: clamp(18px, 3vw, 54px);
            padding-right: clamp(18px, 3vw, 54px);
        }

        .dga-home-body .dga-department-grid {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 0;
            padding: 0;
            overflow: visible;
        }

        .dga-home-body .dga-department-item {
            position: relative;
            flex: 0 0 auto;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
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

        .dga-home-body .dga-department-item:last-child .dga-department-title {
            border-right: 1px solid color-mix(in srgb, #ffffff 14%, transparent);
        }

        .dga-home-body .dga-department-title::before {
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            border-radius: 0;
            opacity: 0;
            transform: scaleX(0);
        }

        .dga-home-body .dga-department-title strong {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .dga-home-body .dga-department-arrow {
            width: auto;
            height: auto;
            border-radius: 0;
            background: transparent;
            font-size: 10px;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-title.is-active,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: color-mix(in srgb, var(--dga-theme-accent) 24%, transparent);
            color: #ffffff;
            box-shadow: inset 0 -3px 0 var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-title:hover::before,
        .dga-home-body .dga-department-title:focus::before,
        .dga-home-body .dga-department-title.is-active::before,
        .dga-home-body .dga-department-item.is-open .dga-department-title::before {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-home-body .dga-department-item:hover .dga-department-arrow,
        .dga-home-body .dga-department-item:focus-within .dga-department-arrow,
        .dga-home-body .dga-department-title.is-active .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: transparent;
            color: inherit;
            transform: rotate(180deg);
        }

        .dga-home-body .dga-department-menu {
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

        .dga-home-body .dga-department-menu::before {
            display: none;
        }

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
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

        .dga-home-body .dga-department-menu a::before {
            display: none;
        }

        .dga-home-body .dga-department-menu a:last-child {
            border-bottom: 0;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            background: color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
            color: #ffffff;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-department-strip .container {

                padding-left: 12px;
                padding-right: 12px;
            }

            .dga-home-body .dga-department-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1px;
            }

            .dga-home-body .dga-department-title,
            .dga-home-body .dga-department-title:hover,
            .dga-home-body .dga-department-title:focus {
                width: 100%;
                min-height: 38px;
                padding: 0 12px;
                border: 0;
                justify-content: center;
            }

            .dga-home-body .dga-department-menu {
                position: static;
                width: 100%;
                min-width: 0;
                margin-top: 0;
                transform: none;
            }
        }

        .dga-home-body .dga-department-strip {
            border-top: 1px solid color-mix(in srgb, var(--dga-theme-light) 20%, transparent);
            border-bottom: 1px solid color-mix(in srgb, var(--dga-theme-dark) 36%, transparent);
            box-shadow: 0 8px 18px rgba(13, 66, 96, .16);
        }

        .dga-home-body .dga-department-strip::before {
            display: none;
        }

        .dga-home-body .dga-department-strip::after {
            height: 1px;
            opacity: .65;
        }

        .dga-home-body .dga-department-strip .container {
            max-width: none;
            padding-left: clamp(18px, 3vw, 54px);
            padding-right: clamp(18px, 3vw, 54px);
        }

        .dga-home-body .dga-department-grid {
            /* display: flex; */
            align-items: stretch;
            justify-content: center;
            gap: 0;
            padding: 0;
            overflow: visible;
        }

        .dga-home-body .dga-department-item {
            position: relative;
            flex: 0 0 auto;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
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

        .dga-home-body .dga-department-item:last-child .dga-department-title {
            border-right: 1px solid color-mix(in srgb, #ffffff 14%, transparent);
        }

        .dga-home-body .dga-department-title::before {
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            border-radius: 0;
            opacity: 0;
            transform: scaleX(0);
        }

        .dga-home-body .dga-department-title strong {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .dga-home-body .dga-department-arrow {
            width: auto;
            height: auto;
            border-radius: 0;
            background: transparent;
            font-size: 10px;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-title.is-active,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: color-mix(in srgb, var(--dga-theme-accent) 24%, transparent);
            color: #ffffff;
            box-shadow: inset 0 -3px 0 var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-title:hover::before,
        .dga-home-body .dga-department-title:focus::before,
        .dga-home-body .dga-department-title.is-active::before,
        .dga-home-body .dga-department-item.is-open .dga-department-title::before {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-home-body .dga-department-item:hover .dga-department-arrow,
        .dga-home-body .dga-department-item:focus-within .dga-department-arrow,
        .dga-home-body .dga-department-title.is-active .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: transparent;
            color: inherit;
            transform: rotate(180deg);
        }

        .dga-home-body .dga-department-menu {
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

        .dga-home-body .dga-department-menu::before {
            display: none;
        }

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
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

        .dga-home-body .dga-department-menu a::before {
            display: none;
        }

        .dga-home-body .dga-department-menu a:last-child {
            border-bottom: 0;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            background: color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
            color: #ffffff;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-department-strip .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .dga-home-body .dga-department-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1px;
            }

            .dga-home-body .dga-department-title,
            .dga-home-body .dga-department-title:hover,
            .dga-home-body .dga-department-title:focus {
                width: 100%;
                min-height: 38px;
                padding: 0 12px;
                border: 0;
                justify-content: center;
            }

            .dga-home-body .dga-department-menu {
                position: static;
                width: 100%;
                min-width: 0;
                margin-top: 0;
                transform: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dga-updates-track {
                animation: none;
            }

            .dga-hero-panel.is-active {
                animation: none;
            }

	            .dga-org-chart {
	                opacity: 1;
	                transform: none;
	                transition: none;
	            }
        }

        .dga-home-body {
            height: 100svh;
            overflow: hidden;
            background: #0b2f45;
        }

        .dga-home-body main {
            height: calc(100svh - var(--dga-fixed-top-height, 164px));
            min-height: 0;
            overflow: hidden;
        }

        .dga-home-body main > .dga-hero,
        .dga-home-body main > .dga-updates-section,
        .dga-home-body main > .dga-section {
            display: none;
        }

        .dga-home-body .dga-footer {
            display: none;
        }

        .dga-home-body .dga-page-sections-band {
            position: relative;
            height: 100%;
	            padding: clamp(8px, 1.4vh, 14px) 0;
            overflow-y: auto;
            overflow-x: hidden;
            border: 0;
            background:
                linear-gradient(115deg, #082f4f 0%, #0b5f7a 46%, #f4f7fb 46%, #eef5f7 100%);
        }

        .dga-home-body .dga-page-sections-band .container {
            height: 100%;
            width: 100%;
            max-width: none;
            display: flex;
            flex-direction: column;
	            gap: 8px;
	            padding-left: clamp(8px, 1vw, 14px);
	            padding-right: clamp(10px, 1.4vw, 20px);
        }

	        .dga-home-body .dga-hero-section-grid {
	            width: 100%;
	            max-width: none;
		            flex: 0 0 clamp(460px, 58vh, 620px);
		            min-height: 500px;
		            display: grid;
		            grid-template-columns: minmax(280px, 330px) minmax(0, 1fr);
		            gap: clamp(10px, 1.6vw, 18px);
	            margin: 0;
            align-items: stretch;
        }

        .dga-home-body .dga-hero-menu-card,
        .dga-home-body .dga-hero-content-shell {
            min-height: 0;
            border: 1px solid rgba(255, 255, 255, .22);
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .22);
        }

        .dga-home-body .dga-hero-menu-card {
            position: relative;
            display: flex;
            flex-direction: column;
	            gap: 11px;
	            padding: 18px 14px 16px 20px;
            border-top: 0;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(248, 251, 255, .99), rgba(231, 239, 249, .97));
            box-shadow: 20px 30px 64px rgba(28, 57, 99, .16);
            overflow: hidden;
        }

        .dga-home-body .dga-hero-menu-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 12px;
            border-radius: 8px 0 0 8px;
            background: #1d4f8f;
            box-shadow: 8px 0 22px rgba(29, 79, 143, .18);
            pointer-events: none;
            z-index: 0;
        }

        .dga-home-body .dga-hero-menu-card::after {
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
            z-index: 0;
        }

        .dga-menu-kicker {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            min-height: 30px;
            margin: 0 0 8px;
            padding: 0 14px 0 32px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            background: rgba(255, 255, 255, .13);
            color: #fff5e7;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
            line-height: 1;
            white-space: nowrap;
            box-shadow: 0 10px 22px rgba(0, 0, 0, .12);
        }

        .dga-menu-kicker::before {
            content: "";
            position: absolute;
            left: 11px;
            top: 50%;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f0b84f;
            box-shadow: 0 0 0 4px rgba(240, 184, 79, .2);
            transform: translateY(-50%);
        }

        .dga-home-body .dga-hero-menu-title {
            position: relative;
            z-index: 1;
            display: inline-block;
            align-self: center;
	            margin: 4px 0 8px;
	            padding: 0 14px 10px;
	            color: #172b49;
	            font-size: 23px;
            font-weight: 900;
            line-height: 1.05;
            text-align: center;
        }

        .dga-home-body .dga-hero-menu-title::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 172px;
            height: 4px;
            border-radius: 999px;
            background: #1d4f8f;
            transform: translateX(-50%);
        }

        .dga-menu-subtitle {
            position: relative;
            z-index: 1;
            margin: 20px 0 5px;
            padding: 11px 12px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-left: 3px solid #7ddbd3;
            border-radius: 7px;
            background: rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .84);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
            box-shadow: 0 10px 22px rgba(0, 0, 0, .1);
        }

        .dga-home-body .dga-hero-menu {
            position: relative;
            z-index: 1;
            flex: 1;
            min-height: 0;
            overflow: visible;
            display: grid;
            align-content: start;
	            gap: 8px;
	            padding: 5px 2px 0;
        }

        .dga-home-body .dga-hero-menu::before {
            display: none;
        }

        .dga-home-body .dga-hero-menu-button {
            position: relative;
	            gap: 9px;
	            min-height: 48px;
	            padding: 8px 12px 8px 13px;
            overflow: hidden;
            border: 1px solid rgba(199, 215, 236, .96);
            border-radius: 8px;
            background: rgba(255, 255, 255, .94);
            color: #172b49;
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
            box-shadow: 0 10px 22px rgba(28, 57, 99, .08);
            transition: transform .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .dga-home-body .dga-hero-menu-button::before {
            display: none;
        }

        .dga-home-body .dga-hero-menu-button::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 7px;
            background: var(--dga-menu-accent, #1d4f8f);
            opacity: 1;
            z-index: 0;
            transition: width .28s ease, opacity .2s ease;
        }

        .dga-home-body .dga-hero-menu-button i,
        .dga-home-body .dga-hero-menu-button.is-active i {
	            width: 30px;
	            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--dga-menu-icon-bg, rgba(49, 83, 122, .1));
            color: inherit;
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-hero-menu-button span {
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-hero-menu-button:hover,
        .dga-home-body .dga-hero-menu-button:focus,
        .dga-home-body .dga-hero-menu-button.is-active {
            transform: translateX(6px);
            border-color: var(--dga-menu-accent, #1d4f8f);
            background: rgba(255, 255, 255, .94);
            color: #fff;
            box-shadow: 0 16px 30px var(--dga-menu-shadow, rgba(29, 79, 143, .22));
            outline: none;
        }

        .dga-home-body .dga-hero-menu-button:hover::after,
        .dga-home-body .dga-hero-menu-button:focus::after,
        .dga-home-body .dga-hero-menu-button.is-active::after {
            width: 100%;
            opacity: 1;
        }

        .dga-home-body .dga-hero-menu-button:hover::before,
        .dga-home-body .dga-hero-menu-button:focus::before,
        .dga-home-body .dga-hero-menu-button.is-active::before {
            display: none;
        }
        .dga-home-body .dga-hero-menu-button:hover i,
        .dga-home-body .dga-hero-menu-button:focus i,
        .dga-home-body .dga-hero-menu-button.is-active i {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }

        .dga-home-body .dga-hero-menu-button:nth-child(1) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(2) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(3) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(4) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(5) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(6) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(7) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-menu-button:nth-child(8) {
            --dga-menu-accent: #1d4f8f;
            --dga-menu-icon-bg: rgba(29, 79, 143, .1);
            --dga-menu-shadow: rgba(29, 79, 143, .22);
        }

        .dga-home-body .dga-hero-content-shell,
        .dga-home-body .dga-hero-content-shell.is-open {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: none;
            height: 100%;
            max-height: 100%;
            padding: 0;
            overflow: hidden;
            color: #1c2a3a;
        }

        .dga-home-intro {
            flex: 0 0 auto;
            padding: clamp(16px, 2vw, 24px) clamp(18px, 2.4vw, 30px);
            border-bottom: 1px solid #d8e2ef;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .98), rgba(239, 248, 249, .96)),
                radial-gradient(circle at right top, rgba(243, 192, 51, .18), transparent 34%);
        }

        .dga-home-intro .dga-eyebrow {
            margin-bottom: 10px;
            border-color: rgba(11, 95, 165, .2);
            background: #eaf5f6;
            color: #027c86;
            font-size: 12px;
        }

        .dga-home-intro h2 {
            max-width: 920px;
            margin: 0 0 10px;
            color: #102234;
            font-size: clamp(26px, 3vw, 40px);
            line-height: 1.1;
            font-weight: 800;
        }

        .dga-home-intro p {
            max-width: 980px;
            margin: 0;
            color: #435268;
            font-size: 15px;
            line-height: 1.6;
        }

        .dga-home-panel {
            position: relative;
            padding: 0 !important;
            overflow: hidden;
            background:
                linear-gradient(180deg, #fffdfa 0%, #f4f8fb 52%, #edf6f4 100%);
        }

        .dga-home-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, transparent 0 56%, rgba(14, 116, 144, .08) 56% 64%, transparent 64%),
                linear-gradient(145deg, transparent 0 72%, rgba(159, 61, 85, .1) 72% 77%, transparent 77%);
            pointer-events: none;
        }

	        .dga-home-panel .dga-home-intro {
	            position: relative;
	            z-index: 1;
	            min-height: 0;
	            display: flex;
	            flex-direction: column;
	            justify-content: center;
	            padding: clamp(24px, 3vw, 44px) clamp(28px, 4vw, 58px);
	            border-bottom: 1px solid #dbe7ec;
	            border-left: 8px solid #0e7490;
	            background:
                linear-gradient(90deg, rgba(255, 255, 255, .96), rgba(248, 253, 252, .9)),
                linear-gradient(135deg, transparent 0 76%, rgba(244, 193, 109, .2) 76% 100%);
        }

        .dga-home-panel .dga-eyebrow {
            align-self: flex-start;
            padding: 8px 13px;
            border-color: rgba(14, 116, 144, .24);
            border-radius: 999px;
            background: #eaf7f7;
            box-shadow: 0 10px 24px rgba(14, 116, 144, .1);
            color: #0e7490;
            font-size: 12px;
            font-weight: 900;
        }

	        .dga-home-panel .dga-home-intro h2 {
	            max-width: 1060px;
	            margin-top: 4px;
	            margin-bottom: 12px;
	            color: #172338;
	            font-size: clamp(30px, 3.4vw, 46px);
	            line-height: 1.06;
	            font-weight: 900;
	        }

        .dga-home-panel .dga-home-intro p {
            max-width: 980px;
            color: #34465e;
            font-size: clamp(15px, 1.2vw, 18px);
            line-height: 1.7;
        }

	        .dga-home-panel .dga-stat-grid {
	            position: relative;
	            z-index: 1;
	            margin: 0;
	            padding: clamp(18px, 2.2vw, 28px);
	            gap: clamp(12px, 1.4vw, 18px);
	            background:
	                linear-gradient(180deg, rgba(246, 250, 252, .92), rgba(255, 255, 255, .98));
	        }

	        .dga-home-panel .dga-stat {
	            position: relative;
	            min-height: 118px;
	            display: flex;
	            flex-direction: column;
	            align-items: center;
	            justify-content: center;
	            padding: 18px 16px;
            overflow: hidden;
            border: 1px solid rgba(194, 211, 226, .9);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #fff, #f8fbfd);
            box-shadow: 0 16px 36px rgba(22, 32, 42, .08);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dga-home-panel .dga-stat::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 6px;
            background: var(--dga-home-stat-accent, #0e7490);
        }

        .dga-home-panel .dga-stat::after {
            content: "";
            position: absolute;
            right: -28px;
            bottom: -28px;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: var(--dga-home-stat-soft, rgba(14, 116, 144, .08));
            pointer-events: none;
        }

        .dga-home-panel .dga-stat:hover {
            transform: translateY(-4px);
            border-color: var(--dga-home-stat-accent, #0e7490);
            box-shadow: 0 24px 54px rgba(22, 32, 42, .16);
        }

        .dga-home-panel .dga-stat strong {
            margin-bottom: 10px;
            color: var(--dga-home-stat-accent, #0e7490);
            font-size: clamp(28px, 3vw, 38px);
            line-height: 1;
            font-weight: 900;
            position: relative;
            z-index: 1;
        }

        .dga-home-panel .dga-stat span {
            max-width: 360px;
            color: #273a52;
            font-size: 15px;
            line-height: 1.55;
            position: relative;
            z-index: 1;
        }

        .dga-home-panel .dga-stat:nth-child(1) {
            --dga-home-stat-accent: #0e7490;
            --dga-home-stat-soft: rgba(14, 116, 144, .1);
        }

        .dga-home-panel .dga-stat:nth-child(2) {
            --dga-home-stat-accent: #9f3d55;
            --dga-home-stat-soft: rgba(159, 61, 85, .1);
        }

        .dga-home-panel .dga-stat:nth-child(3) {
            --dga-home-stat-accent: #b77b31;
            --dga-home-stat-soft: rgba(183, 123, 49, .12);
        }

        .dga-overview-panel {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(242, 249, 250, .97)),
                radial-gradient(circle at right top, rgba(11, 95, 165, .12), transparent 34%);
        }

        .dga-overview-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(125deg, transparent 0 62%, rgba(2, 124, 134, .08) 62% 74%, transparent 74%),
                linear-gradient(145deg, transparent 0 70%, rgba(243, 192, 51, .16) 70% 76%, transparent 76%);
            pointer-events: none;
        }

        .dga-overview-panel > h3 {
            position: relative;
            z-index: 1;
            margin: 0 0 22px;
            padding: 22px 24px 22px 86px;
            border: 1px solid rgba(216, 226, 239, .94);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff, #f3fbfc);
            box-shadow: 0 18px 42px rgba(22, 32, 42, .08);
        }

        .dga-overview-panel > h3::before {
            content: "\f19c";
            position: absolute;
            left: 22px;
            top: 50%;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: linear-gradient(135deg, #0b5fa5, #027c86);
            color: #fff;
            font-family: "Font Awesome 5 Free";
            font-size: 18px;
            font-weight: 900;
            transform: translateY(-50%);
            box-shadow: 0 14px 28px rgba(11, 95, 165, .22);
        }

        .dga-overview-panel .dga-hero-card-grid {
            position: relative;
            z-index: 1;
            gap: clamp(16px, 2vw, 24px);
        }

        .dga-overview-panel .dga-hero-info-card {
            position: relative;
            min-height: 100%;
            padding: 26px 24px 24px 76px;
            overflow: hidden;
            border: 1px solid rgba(198, 214, 228, .96);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #fff, #f8fbfd);
            box-shadow: 0 18px 42px rgba(22, 32, 42, .1);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dga-overview-panel .dga-hero-info-card::before {
            content: "\f1ad";
            position: absolute;
            left: 22px;
            top: 24px;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(11, 95, 165, .1);
            color: #0b5fa5;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .dga-overview-panel .dga-hero-info-card:nth-child(2)::before {
            content: "\f0ae";
            background: rgba(183, 119, 8, .12);
            color: #9a6508;
        }

        .dga-overview-panel .dga-hero-info-card:hover {
            transform: translateY(-5px);
            border-color: rgba(11, 95, 165, .32);
            box-shadow: 0 26px 58px rgba(22, 32, 42, .15);
        }

        .dga-overview-panel .dga-hero-info-card h4 {
            margin-bottom: 14px;
            color: #102234;
            font-size: 22px;
            font-weight: 900;
        }

        .dga-overview-panel .dga-hero-list {
            display: grid;
            gap: 10px;
        }

        .dga-overview-panel .dga-hero-list li {
            min-height: 54px;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 11px 12px 11px 34px;
            border: 1px solid #d8e2ef;
            border-radius: 6px;
            background: #fff;
            color: #34465e;
            line-height: 1.5;
        }

        .dga-overview-panel .dga-hero-list li::before {
            left: 13px;
            top: 50%;
            width: 8px;
            height: 8px;
            background: #027c86;
            box-shadow: 0 0 0 4px rgba(2, 124, 134, .1);
            transform: translateY(-50%);
        }

	        .dga-home-body .dga-hero-panel {
	            flex: 1;
	            min-height: 0;
	            overflow-y: auto;
	            padding: clamp(12px, 1.5vw, 20px);
	        }

        .dga-home-body .dga-hero-panel.is-active {
            display: block;
        }

        .dga-home-body .dga-hero-panel h3 {
            color: #102234;
	            font-size: clamp(19px, 1.65vw, 25px);
	        }

	        .dga-home-body .dga-hero-info-card,
	        .dga-home-body .dga-hero-mini-step {
	            padding: 13px;
	        }

	        .dga-home-body .dga-hero-info-card h4,
	        .dga-home-body .dga-hero-mini-step h4 {
	            font-size: 15px;
	        }

	        .dga-home-body .dga-hero-info-card p,
	        .dga-home-body .dga-hero-mini-step p,
	        .dga-home-body .dga-hero-list li {
	            font-size: 13px;
	            line-height: 1.42;
	        }

        .dga-home-body .dga-stat,
        .dga-home-body .dga-hero-info-card,
        .dga-home-body .dga-hero-mini-step {
            border-color: #d8e2ef;
            background: #fff;
            color: #223249;
            box-shadow: 0 12px 30px rgba(22, 32, 42, .08);
        }

        .dga-home-body .dga-stat strong,
        .dga-home-body .dga-hero-info-card h4,
        .dga-home-body .dga-hero-mini-step h4 {
            color: #0b5fa5;
        }

        .dga-home-body .dga-hero-info-card p,
        .dga-home-body .dga-hero-mini-step p,
        .dga-home-body .dga-hero-list li {
            color: #435268;
        }

        .dga-home-body .dga-hero-process-grid .dga-hero-mini-step {
            border-color: rgba(185, 205, 222, .9);
            background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 58%, #eef7f6 100%);
            box-shadow: 0 18px 38px rgba(22, 32, 42, .1);
        }

        .dga-home-body .dga-hero-process-grid .dga-hero-mini-step h4 {
            color: #14324c;
        }

        .dga-home-body .dga-hero-process-grid .dga-hero-mini-step p {
            color: #5a6d80;
        }

        .dga-home-body .dga-hero-flow-root {
            color: #fff;
            background: linear-gradient(135deg, #063b67, #0b5fa5);
        }

        .dga-home-body .dga-flow-mindmap {
            position: relative;
            min-height: 430px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 190px minmax(0, 1fr);
            gap: 24px;
            align-items: center;
            padding: 24px 10px 8px;
        }

        .dga-home-body .dga-flow-mindmap::before {
            content: "";
            position: absolute;
            left: 13%;
            right: 13%;
            top: 50%;
            height: 3px;
            border-radius: 999px;
            background: #bfd6e7;
            transform: translateY(-50%);
        }

        .dga-home-body .dga-flow-center-node,
        .dga-home-body .dga-flow-branch-card,
        .dga-home-body .dga-flow-leaf {
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-flow-center-node {
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            border: 6px solid #e8f2f8;
            border-radius: 50%;
            background: linear-gradient(135deg, #063b67, #0b5fa5);
            color: #fff;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.25;
            text-align: center;
            box-shadow: 0 18px 36px rgba(6, 59, 103, .26);
        }

        .dga-home-body .dga-flow-branch-card {
            min-height: 330px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            border: 1px solid #d8e6f0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 34px rgba(22, 32, 42, .1);
        }

        .dga-home-body .dga-flow-branch-card::before {
            content: "";
            position: absolute;
            top: 50%;
            width: 24px;
            height: 3px;
            border-radius: 999px;
            background: #bfd6e7;
            transform: translateY(-50%);
        }

        .dga-home-body .dga-flow-branch-card.is-left::before {
            right: -25px;
        }

        .dga-home-body .dga-flow-branch-card.is-right::before {
            left: -25px;
        }

        .dga-home-body .dga-flow-branch-title {
            margin: 0;
            padding: 11px 12px;
            border-radius: 8px;
            background: #edf6f8;
            color: #063b67;
            font-size: 16px;
            font-weight: 900;
            text-align: center;
        }

        .dga-home-body .dga-flow-leaves {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dga-home-body .dga-flow-leaf {
            padding: 9px 11px 9px 15px;
            border: 1px solid #d8e6f0;
            border-left: 4px solid #0b5fa5;
            border-radius: 8px;
            background: #f9fcff;
            color: #33465c;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
        }

        .dga-home-body .dga-flow-leaf:nth-child(2n) {
            border-left-color: #b77708;
            background: #fffaf0;
        }

        .dga-home-body .dga-flow-leaf:nth-child(3n) {
            border-left-color: #2d7b4f;
            background: #f4fbf7;
        }

        .dga-home-body .dga-flow-orgmap {
            position: relative;
            max-width: 940px;
            min-height: 520px;
            margin: 8px auto 0;
            padding: 22px 18px 18px;
            color: #142235;
            border: 1px solid #d7e5f0;
            border-radius: 22px;
            background:
                radial-gradient(circle at top left, rgba(14, 116, 144, .16), transparent 34%),
                radial-gradient(circle at right 16% bottom 10%, rgba(183, 123, 49, .16), transparent 26%),
                linear-gradient(160deg, #f9fcff, #eef6fb 56%, #f8fbff);
            box-shadow: 0 24px 54px rgba(22, 32, 42, .12);
            overflow: hidden;
        }

        .dga-home-body .dga-flow-orgmap::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(128deg, transparent 0 60%, rgba(11, 95, 165, .08) 60% 73%, transparent 73% 100%);
            pointer-events: none;
        }

        .dga-home-body .dga-flow-org-caption {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 62px 58px;
            margin-bottom: 18px;
            padding: 0 4px;
        }

        .dga-home-body .dga-flow-org-caption span {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(9, 60, 103, .08);
            color: #0c3f67;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-flow-org-caption p {
            margin: 0;
            max-width: 320px;
            color: #496072;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
            text-align: right;
        }

        .dga-home-body .dga-flow-org-root {
            position: relative;
            z-index: 2;
            width: min(100%, 300px);
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            padding: 14px 24px;
            border: 1px solid rgba(14, 116, 144, .22);
            border-radius: 20px;
            background: linear-gradient(135deg, #0f4f79, #0e7490 54%, #16a085);
            color: #fff;
            font-size: 21px;
            font-weight: 900;
            line-height: 1.15;
            text-align: center;
            box-shadow: 0 18px 36px rgba(10, 71, 109, .24);
        }

        .dga-home-body .dga-flow-org-root::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 100%;
            width: 2px;
            height: 20px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-org-split {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(0, .94fr);
            gap: 22px;
            margin-top: 34px;
        }

        .dga-home-body .dga-flow-org-split::before {
            content: "";
            position: absolute;
            left: 24%;
            right: 24%;
            top: -18px;
            height: 2px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-org-column {
            position: relative;
            min-width: 0;
            padding: 16px;
            border: 1px solid rgba(216, 230, 239, .9);
            border-radius: 18px;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 12px 28px rgba(22, 32, 42, .07);
        }

        .dga-home-body .dga-flow-org-column::before {
            content: "";
            position: absolute;
            left: 50%;
            top: -18px;
            width: 2px;
            height: 18px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-org-head {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border: 1px solid rgba(183, 123, 49, .28);
            border-radius: 16px;
            background: linear-gradient(135deg, #fff7df, #f8d482 58%, #f3c66f);
            color: #4b3412;
            font-size: 17px;
            font-weight: 900;
            text-align: center;
            box-shadow: 0 12px 24px rgba(183, 123, 49, .14);
        }

        .dga-home-body .dga-flow-org-head::before {
            content: "\f0e8";
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-weight: 900;
            color: #b77b31;
            font-size: 14px;
        }

        .dga-home-body .dga-flow-org-subgrid {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, .95fr) minmax(0, 1fr);
            gap: 18px;
            margin-top: 20px;
            padding-top: 0;
        }

        .dga-home-body .dga-flow-org-subgrid::before {
            content: "";
            position: absolute;
            left: 25%;
            right: 25%;
            top: -12px;
            height: 2px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-subbranch,
        .dga-home-body .dga-flow-field-list {
            position: relative;
        }

        .dga-home-body .dga-flow-subbranch::before {
            content: "";
            position: absolute;
            left: 50%;
            top: -12px;
            width: 2px;
            height: 12px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-box {
            position: relative;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 13px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.25;
            text-align: center;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .dga-home-body .dga-flow-box::after {
            content: "\f111";
            flex: 0 0 auto;
            order: -1;
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-weight: 900;
            font-size: 7px;
            opacity: .76;
        }

        .dga-home-body .dga-flow-box.is-blue {
            border: 1px solid rgba(14, 116, 144, .22);
            background: linear-gradient(135deg, #ffffff, #ecf7fb);
            color: #174b7a;
            box-shadow: 0 10px 20px rgba(14, 116, 144, .08);
        }

        .dga-home-body .dga-flow-box.is-green {
            border: 1px solid rgba(78, 143, 91, .22);
            background: linear-gradient(135deg, #ffffff, #eaf7ee);
            color: #28613a;
            box-shadow: 0 10px 20px rgba(78, 143, 91, .08);
        }

        .dga-home-body .dga-flow-box:hover {
            transform: translateY(-2px);
        }

        .dga-home-body .dga-flow-box.is-link {
            justify-content: space-between;
            padding: 13px 16px;
            text-align: left;
            text-decoration: none;
            color: #18324c;
        }

        .dga-home-body .dga-flow-box.is-link span {
            flex: 1 1 auto;
        }

        .dga-home-body .dga-flow-box.is-link i {
            flex: 0 0 auto;
            font-size: 12px;
            opacity: .82;
            transition: transform .22s ease;
        }

        .dga-home-body .dga-flow-box.is-link:hover,
        .dga-home-body .dga-flow-box.is-link:focus {
            border-color: rgba(14, 116, 144, .38);
            box-shadow: 0 16px 28px rgba(14, 116, 144, .14);
            text-decoration: none;
            color: #0f2c47;
        }

        .dga-home-body .dga-flow-box.is-link:hover i,
        .dga-home-body .dga-flow-box.is-link:focus i {
            transform: translateX(4px);
        }

        .dga-home-body .dga-flow-child {
            width: 78%;
            margin: 20px auto 0;
        }

        .dga-home-body .dga-flow-child::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 100%;
            width: 2px;
            height: 20px;
            background: #99c89c;
        }

        .dga-home-body .dga-flow-division-list,
        .dga-home-body .dga-flow-field-list {
            display: grid;
            gap: 13px;
            margin-top: 20px;
        }

        .dga-home-body .dga-flow-division-list {
            position: relative;
            padding-left: 24px;
        }

        .dga-home-body .dga-flow-division-list::before {
            content: "";
            position: absolute;
            left: 12px;
            top: -20px;
            bottom: 27px;
            width: 2px;
            background: #99c89c;
        }

        .dga-home-body .dga-flow-division-list .dga-flow-box::before {
            content: "";
            position: absolute;
            right: 100%;
            top: 50%;
            width: 12px;
            height: 2px;
            background: #99c89c;
        }

        .dga-home-body .dga-flow-field-list {
            margin-top: 20px;
            margin-left: 28px;
            padding-left: 16px;
        }

        .dga-home-body .dga-flow-field-list::before {
            content: "";
            position: absolute;
            left: 0;
            top: -20px;
            bottom: 27px;
            width: 2px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box::before {
            content: "";
            position: absolute;
            right: 100%;
            top: 50%;
            width: 16px;
            height: 2px;
            background: #a9c8dd;
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box:not(.is-link):nth-child(2n) {
            border-color: rgba(14, 116, 144, .26);
            background: linear-gradient(135deg, #fffdfa, #eef8ff);
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box:not(.is-link):nth-child(3n) {
            background: linear-gradient(135deg, #fdfefe, #eefaf7);
        }

        .dga-home-body .dga-flow-orgmap {
            display: grid;
            gap: 18px;
            width: 100%;
            max-width: none;
            min-height: 0;
            padding: 24px 18px 28px;
            border: 1px solid rgba(220, 229, 238, .96);
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
            box-shadow: 0 20px 46px rgba(22, 32, 42, .08);
            overflow: hidden;
        }

        .dga-home-body .dga-flow-orgmap::before,
        .dga-home-body .dga-flow-org-root::after,
        .dga-home-body .dga-flow-org-split::before,
        .dga-home-body .dga-flow-org-column::before,
        .dga-home-body .dga-flow-org-subgrid::before,
        .dga-home-body .dga-flow-subbranch::before,
        .dga-home-body .dga-flow-child::before,
        .dga-home-body .dga-flow-division-list::before,
        .dga-home-body .dga-flow-field-list::before,
        .dga-home-body .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body .dga-flow-field-list .dga-flow-box::before {
            display: none;
        }

        .dga-home-body .dga-flow-org-caption {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 0;
            padding: 0 0 14px;
            border-bottom: 1px solid #e3eaf1;
        }

        .dga-home-body .dga-flow-org-caption span {
            position: relative;
            padding: 0 0 10px;
            background: none;
            border: 0;
            color: #14324c;
            box-shadow: none;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: .06em;
        }

        .dga-home-body .dga-flow-org-caption span::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 88px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #0f4f79, #d3864f);
        }

        .dga-home-body .dga-flow-org-caption p {
            max-width: 520px;
            color: #607082;
            text-align: right;
        }

        .dga-home-body .dga-flow-org-root {
            width: min(100%, 380px);
            min-height: 82px;
            justify-content: center;
            margin: 0 auto;
            padding: 16px 28px;
            border-radius: 12px;
            border: 1px solid rgba(221, 119, 18, .18);
            background: linear-gradient(180deg, #ffb45e, #f47a18);
            color: #113457;
            box-shadow: 0 12px 24px rgba(244, 122, 24, .16);
            font-size: 23px;
            text-align: center;
            font-weight: 900;
            letter-spacing: .01em;
        }

        .dga-home-body .dga-flow-org-root::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 100%;
            width: 2px;
            height: 16px;
            background: #c4d0dc;
            display: block;
            transform: translateX(-50%);
        }

        .dga-home-body .dga-flow-direction-band {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
            width: min(100%, 700px);
            margin: -2px auto 0;
            position: relative;
            pointer-events: none;
        }

        .dga-home-body .dga-flow-direction-band::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 2px;
            background: #c4d0dc;
            display: block;
        }

        .dga-home-body .dga-flow-direction-band span {
            position: relative;
            display: block;
            height: 20px;
        }

        .dga-home-body .dga-flow-direction-band span::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            width: 2px;
            height: 20px;
            background: #c4d0dc;
            display: block;
            transform: translateX(-50%);
        }

        .dga-home-body .dga-flow-direction-band .is-left::after,
        .dga-home-body .dga-flow-direction-band .is-right::after {
            content: none;
        }

        .dga-home-body .dga-flow-org-split {
            position: relative;
            grid-template-columns: minmax(0, 1.02fr) minmax(0, .98fr);
            gap: 22px;
            margin-top: 0;
        }

        .dga-home-body .dga-flow-org-column {
            position: relative;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .dga-home-body .dga-flow-org-column::before {
            content: "";
            position: absolute;
            left: 50%;
            top: -20px;
            width: 2px;
            height: 20px;
            background: #c4d0dc;
            display: block;
            transform: translateX(-50%);
        }

        .dga-home-body .dga-flow-org-head {
            justify-content: center;
            min-height: 52px;
            padding: 10px 18px;
            border-radius: 0;
            font-size: 17px;
            font-weight: 400;
            box-shadow: none;
        }

        .dga-home-body .dga-flow-org-column.is-hq .dga-flow-org-head,
        .dga-home-body .dga-flow-org-column.is-field .dga-flow-org-head {
            border: 0;
            background: linear-gradient(180deg, #ffe071, #ffcb38);
            color: #173455;
            box-shadow: inset 0 -1px 0 rgba(160, 114, 14, .12);
        }

        .dga-home-body .dga-flow-column-note {
            margin: 10px 0 0;
            color: #556678;
            font-size: 12px;
            line-height: 1.45;
            text-align: center;
        }

        .dga-home-body .dga-flow-org-subgrid {
            position: relative;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-top: 14px;
            padding-top: 14px;
        }

        .dga-home-body .dga-flow-org-subgrid::before {
            content: "";
            position: absolute;
            left: 25%;
            right: 25%;
            top: 0;
            height: 2px;
            background: #c4d0dc;
            display: block;
        }

        .dga-home-body .dga-flow-subbranch {
            position: relative;
            padding: 14px 0 0;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .dga-home-body .dga-flow-subbranch::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            width: 2px;
            height: 14px;
            background: #c4d0dc;
            display: block;
            transform: translateX(-50%);
        }

        .dga-home-body .dga-flow-box {
            min-height: 54px;
            padding: 12px 14px;
            border-radius: 8px;
            justify-content: center;
            text-align: center;
            box-shadow: 0 8px 16px rgba(22, 32, 42, .07);
            font-weight: 700;
        }

        .dga-home-body .dga-flow-box.is-blue {
            border: 0;
            background: linear-gradient(180deg, #5d88de, #3463c4);
            color: #fff;
        }

        .dga-home-body .dga-flow-box.is-green {
            border: 0;
            background: linear-gradient(180deg, #7cc651, #63ad3b);
            color: #fff;
        }

        .dga-home-body .dga-flow-subbranch:first-child > .dga-flow-box.is-blue {
            background: linear-gradient(180deg, #5d88de, #3463c4);
        }

        .dga-home-body .dga-flow-subbranch:first-child > .dga-flow-child {
            background: linear-gradient(180deg, #7cc651, #63ad3b);
        }

        .dga-home-body .dga-flow-subbranch:last-child > .dga-flow-box.is-blue {
            background: linear-gradient(180deg, #5d88de, #3463c4);
        }

        .dga-home-body .dga-flow-child {
            width: 82%;
            margin: 20px auto 0;
        }

        .dga-home-body .dga-flow-child::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 100%;
            width: 2px;
            height: 20px;
            background: #9fcf8c;
            display: block;
            transform: translateX(-50%);
        }

        .dga-home-body .dga-flow-division-list,
        .dga-home-body .dga-flow-field-list {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 20px;
            margin-left: 0;
            padding-left: 24px;
            position: relative;
        }

        .dga-home-body .dga-flow-division-list::before {
            content: "";
            position: absolute;
            left: 12px;
            top: -20px;
            bottom: 26px;
            width: 2px;
            background: #a2cc86;
            display: block;
        }

        .dga-home-body .dga-flow-field-list::before {
            content: "";
            position: absolute;
            left: 12px;
            top: -20px;
            bottom: 26px;
            width: 2px;
            background: #c4d0dc;
            display: block;
        }

        .dga-home-body .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body .dga-flow-field-list .dga-flow-box::before {
            content: "";
            position: absolute;
            right: 100%;
            top: 50%;
            width: 12px;
            height: 2px;
            display: block;
        }

        .dga-home-body .dga-flow-division-list .dga-flow-box::before {
            background: #a2cc86;
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box::before {
            background: #c4d0dc;
        }

        .dga-home-body .dga-flow-box::after {
            content: none;
        }

        .dga-home-body .dga-flow-box.is-link {
            min-height: 86px;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border: 0;
            border-top: 0;
            border-left: 0;
            border-radius: 8px;
            background: linear-gradient(180deg, #5d88de, #3463c4);
            box-shadow: 0 8px 16px rgba(22, 32, 42, .08);
        }

        .dga-home-body .dga-flow-box.is-link:hover,
        .dga-home-body .dga-flow-box.is-link:focus {
            transform: none;
            border-color: transparent;
            box-shadow: none;
            text-decoration: none;
        }

        .dga-home-body .dga-flow-box-copy strong,
        .dga-home-body .dga-flow-box-copy small,
        .dga-home-body .dga-flow-mini-tag,
        .dga-home-body .dga-flow-box.is-link i {
            color: #fff;
        }

        .dga-home-body .dga-flow-mini-tag {
            background: rgba(255, 255, 255, .14);
        }

        .dga-home-body .dga-flow-box.is-link i {
            background: rgba(255, 255, 255, .14);
        }

        .dga-home-body .dga-flow-box.is-link::after {
            content: none;
        }

        .dga-home-body .dga-flow-box.is-link:hover,
        .dga-home-body .dga-flow-box.is-link:focus {
            transform: none;
            border-color: transparent;
            box-shadow: none;
        }

        .dga-home-body .dga-flow-box-copy {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .dga-home-body .dga-flow-box-copy strong {
            display: block;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.35;
        }

        .dga-home-body .dga-flow-box-copy small {
            display: block;
            color: rgba(255, 255, 255, .92);
            font-size: 12px;
            line-height: 1.55;
        }

        .dga-home-body .dga-flow-mini-tag {
            display: inline-flex;
            align-self: flex-start;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            color: #fff;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-flow-box.is-link i {
            margin-left: auto;
            margin-top: 2px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            color: #fff;
        }

        .dga-home-body .dga-wing-network {
            position: relative;
            margin-top: 14px;
        }

        .dga-home-body .dga-wing-connection-grid {
            position: relative;
            display: grid;
            gap: 14px;
            padding-left: 60px;
        }

        .dga-home-body .dga-wing-connection-grid::before {
            content: "";
            position: absolute;
            left: 25px;
            top: 10px;
            bottom: 24px;
            width: 2px;
            background: linear-gradient(180deg, #d0dceb, #b8cbdd);
        }

        .dga-home-body .dga-wing-link-card {
            position: relative;
            padding: 18px 20px 18px 78px;
            border: 1px solid #d8e4ef;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff, #f8fbff);
            box-shadow: 0 12px 28px rgba(22, 32, 42, .08);
        }

        .dga-home-body .dga-wing-link-card::before {
            content: "";
            position: absolute;
            left: -35px;
            top: 38px;
            width: 35px;
            height: 2px;
            background: #c7d7e6;
        }

        .dga-home-body .dga-wing-link-card::after {
            content: "";
            position: absolute;
            left: -43px;
            top: 31px;
            width: 16px;
            height: 16px;
            border: 3px solid #fff;
            border-radius: 999px;
            background: linear-gradient(180deg, #5d88de, #3463c4);
            box-shadow: 0 4px 10px rgba(52, 99, 196, .16);
        }

        .dga-home-body .dga-wing-link-card:nth-child(2n)::after {
            background: linear-gradient(180deg, #67bfb4, #2d8b80);
            box-shadow: 0 4px 10px rgba(45, 139, 128, .16);
        }

        .dga-home-body .dga-wing-link-card:nth-child(3n)::after {
            background: linear-gradient(180deg, #79c24c, #63ad3b);
            box-shadow: 0 4px 10px rgba(99, 173, 59, .16);
        }

        .dga-home-body .dga-wing-link-card:nth-child(4n)::after {
            background: linear-gradient(180deg, #ffb45e, #f47a18);
            box-shadow: 0 4px 10px rgba(244, 122, 24, .16);
        }

        .dga-home-body .dga-wing-link-card:nth-child(5n)::after {
            background: linear-gradient(180deg, #8b85dc, #5b53b4);
            box-shadow: 0 4px 10px rgba(91, 83, 180, .16);
        }

        .dga-home-body .dga-wing-link-card h4 {
            margin: 0 0 8px;
            color: #14324c;
            font-size: 18px;
            font-weight: 800;
        }

        .dga-home-body .dga-wing-link-card p {
            margin: 0;
            color: #56687d;
            font-size: 14px;
            line-height: 1.6;
        }

        .dga-home-body .dga-wing-icon {
            position: absolute;
            left: 20px;
            top: 18px;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(180deg, #eef4ff, #dfe9fb);
            color: #315fc0;
            font-size: 18px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .7);
        }

        .dga-home-body .dga-wing-link-card:nth-child(2n) .dga-wing-icon {
            background: linear-gradient(180deg, #edf8f7, #d8f0ed);
            color: #2d8b80;
        }

        .dga-home-body .dga-wing-link-card:nth-child(3n) .dga-wing-icon {
            background: linear-gradient(180deg, #f1f9ea, #e1f0d3);
            color: #5c9935;
        }

        .dga-home-body .dga-wing-link-card:nth-child(4n) .dga-wing-icon {
            background: linear-gradient(180deg, #fff4e8, #ffe6cd);
            color: #d87320;
        }

        .dga-home-body .dga-wing-link-card:nth-child(5n) .dga-wing-icon {
            background: linear-gradient(180deg, #f2efff, #e1dcfb);
            color: #5b53b4;
        }

        .dga-home-body .dga-roadmap-network {
            position: relative;
            margin-top: 16px;
            padding-left: 40px;
        }

        .dga-home-body .dga-roadmap-network::before {
            content: "";
            position: absolute;
            left: 14px;
            top: 18px;
            bottom: 18px;
            width: 4px;
            border-radius: 999px;
            background: linear-gradient(180deg, #1f4a72 0%, #2f8b97 54%, #e8a63d 100%);
            box-shadow: 0 0 0 8px rgba(220, 233, 244, .58);
        }

        .dga-home-body .dga-roadmap-connection-list {
            display: grid;
            gap: 18px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dga-home-body .dga-roadmap-link-card {
            position: relative;
            min-height: 154px;
            padding: 24px 22px 24px 96px;
            border: 1px solid rgba(185, 205, 222, .85);
            border-radius: 22px;
            background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 56%, #eef7f6 100%);
            box-shadow: 0 18px 34px rgba(20, 40, 64, .09);
            overflow: hidden;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(odd) {
            margin-right: 34px;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even) {
            margin-left: 48px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 48%, #fff4e8 100%);
        }

        .dga-home-body .dga-roadmap-link-card::before {
            content: "";
            position: absolute;
            left: -26px;
            top: 41px;
            width: 26px;
            height: 2px;
            background: #9ab8cb;
        }

        .dga-home-body .dga-roadmap-link-card::after {
            content: "";
            position: absolute;
            left: -34px;
            top: 32px;
            width: 20px;
            height: 20px;
            border: 4px solid #fff;
            border-radius: 999px;
            background: linear-gradient(180deg, #1f4a72, #2f8b97);
            box-shadow: 0 8px 18px rgba(31, 74, 114, .18);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even)::after {
            background: linear-gradient(180deg, #d38b2b, #e8a63d);
            box-shadow: 0 8px 18px rgba(211, 139, 43, .18);
        }

        .dga-home-body .dga-roadmap-link-card > * {
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
            position: absolute;
            left: 20px;
            top: 24px;
            width: 56px;
            height: 56px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(180deg, #edf4ff, #dce9fb);
            color: #285884;
            font-size: 21px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .82);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even) .dga-roadmap-step {
            background: linear-gradient(180deg, #fff4e7, #ffe3bc);
            color: #c37714;
        }

        .dga-home-body .dga-roadmap-phase {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #153959, #2f8b97);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even) .dga-roadmap-phase {
            background: linear-gradient(90deg, #b7741e, #e0a037);
        }

        .dga-home-body .dga-roadmap-phase::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .88);
        }

        .dga-home-body .dga-roadmap-link-card h4 {
            margin: 0 0 8px;
            color: #14324c;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.35;
        }

        .dga-home-body .dga-roadmap-link-card p {
            margin: 0;
            color: #5a6d80;
            font-size: 14px;
            line-height: 1.65;
        }

        .dga-home-body .dga-hero-roadmap-list li {
            background: #fff;
            color: #435268;
            border: 1px solid #d8e2ef;
            box-shadow: 0 12px 30px rgba(22, 32, 42, .08);
        }

        .dga-home-body .dga-hero-roadmap-list strong {
            color: #102234;
        }

        .dga-bottom-updates {
            flex: 0 0 auto;
            display: grid;
            grid-template-columns: minmax(170px, .9fr) minmax(0, 1.7fr) minmax(170px, .9fr);
            gap: 18px;
            align-items: stretch;
            padding: 18px;
            border: 1px solid rgba(12, 48, 78, .08);
            border-radius: 18px;
            background: linear-gradient(180deg, #f9fcff, #eef5f8);
            box-shadow: 0 18px 40px rgba(22, 32, 42, .1);
        }

        .dga-bottom-updates-center {
            min-width: 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(13, 60, 92, .12);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 14px 32px rgba(18, 37, 54, .08);
            overflow: hidden;
        }

        .dga-update-photo-frame {
            position: relative;
            min-height: clamp(320px, 36vh, 420px);
            display: flex;
            align-items: stretch;
            justify-content: center;
            overflow: hidden;
            border: 1px dashed rgba(18, 118, 130, .22);
            border-radius: 18px;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, .98), rgba(239, 247, 252, .96)),
                radial-gradient(circle at top right, rgba(17, 117, 132, .12), transparent 42%);
            color: #0f4f78;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .82);
        }

        .dga-update-photo-frame::before {
            content: "";
            position: absolute;
            inset: 14px;
            border: 1px solid rgba(129, 190, 204, .18);
            border-radius: 14px;
            pointer-events: none;
        }

        .dga-update-frame-content {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 20px;
            text-align: center;
        }

        .dga-update-frame-content i {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(16, 86, 115, .08);
            color: #0f6880;
            font-size: 18px;
            box-shadow: 0 10px 24px rgba(16, 86, 115, .08);
        }

        .dga-update-frame-label {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(16, 86, 115, .08);
            color: #0f4f78;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-update-frame-note {
            max-width: 170px;
            color: #6a7e92;
            font-size: 13px;
            line-height: 1.6;
        }

        .dga-bottom-updates-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            background: linear-gradient(90deg, #0c3455 0%, #0e5977 56%, #117584 100%);
            color: #fff;
        }

        .dga-bottom-updates-head span {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .84);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-bottom-updates-head h3 {
            margin: 0;
            color: #fff;
            font-size: 23px;
            font-weight: 900;
            line-height: 1.1;
        }

        .dga-bottom-updates-window {
            min-height: 0;
            padding: 14px;
            background: linear-gradient(180deg, #f8fbff, #eef5fb);
        }

        .dga-bottom-updates-window .dga-updates-track {
            display: grid;
            gap: 10px;
            max-height: clamp(290px, 38vh, 408px);
            overflow-y: auto;
            padding-right: 4px;
            animation: none;
        }

        .dga-bottom-updates-window .dga-updates-track::-webkit-scrollbar {
            width: 8px;
        }

        .dga-bottom-updates-window .dga-updates-track::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(13, 89, 119, .28);
        }

        .dga-bottom-updates-window .dga-update-item {
            min-height: 0;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
        }

        .dga-update-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(13, 65, 98, .1);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(18, 37, 54, .06);
        }

        .dga-update-date-badge {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 76px;
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 10px;
            background: linear-gradient(180deg, #f2c257, #dfa232);
            color: #23344a;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .03em;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38);
        }

        .dga-update-card p {
            margin: 0;
            color: #31455b;
            font-size: 13px;
            line-height: 1.65;
        }

        .dga-home-body .dga-flow-map {
            overflow: auto;
            max-height: 100%;
        }

        .dga-home-body .dga-org-chart {
            opacity: 1;
            transform: none;
        }

        .dga-home-body .dga-section-title {
            margin-bottom: 18px;
        }

        .dga-home-body .dga-section-title h3 {
            font-size: clamp(22px, 2vw, 28px);
        }

        .dga-home-body .dga-updates-panel {
            width: 100%;
            min-height: 0;
            height: min(430px, 52vh);
        }

        .dga-home-body .dga-updates-window {
            height: auto;
        }

        .dga-home-footer-strip {
            position: relative;
            min-width: 0;
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr) minmax(0, .95fr);
            gap: 0;
            margin: 0 14px 14px;
            padding: 22px 22px 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, #072b46 0%, #0a4764 48%, #0d6573 100%);
            box-shadow: 0 18px 40px rgba(14, 29, 44, .16);
            color: #fff;
            overflow: hidden;
        }

        .dga-home-footer-strip::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #d4a141, #89dce5, #d4a141);
        }

        .dga-home-footer-brand,
        .dga-home-footer-center,
        .dga-home-footer-meta {
            position: relative;
            z-index: 1;
            min-width: 0;
            padding: 8px 16px;
        }

        .dga-home-footer-center,
        .dga-home-footer-meta {
            border-left: 1px solid rgba(255, 255, 255, .12);
        }

        .dga-home-footer-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .dga-home-footer-brand img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            background: rgba(255, 255, 255, .95);
            border-radius: 14px;
            padding: 5px;
        }

        .dga-home-footer-brand h3 {
            margin: 0;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .dga-home-footer-brand span,
        .dga-home-footer-note,
        .dga-home-footer-meta p {
            display: block;
            margin: 4px 0 0;
            color: rgba(255, 255, 255, .8);
            font-size: 13px;
            line-height: 1.6;
        }

        .dga-home-footer-heading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            color: #f6d37a;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-home-footer-nav {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .dga-home-footer-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            background: rgba(255, 255, 255, .08);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            text-decoration: none;
            transition: background-color .2s ease, transform .2s ease, border-color .2s ease;
        }

        .dga-home-footer-link:hover,
        .dga-home-footer-link:focus {
            background: rgba(255, 255, 255, .14);
            border-color: rgba(137, 220, 229, .38);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .dga-home-footer-link i,
        .dga-home-footer-heading i,
        .dga-home-footer-mail i {
            color: #8fe0e8;
        }

        .dga-home-footer-mail {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
        }

        .dga-home-footer-mail:hover,
        .dga-home-footer-mail:focus {
            color: #f6d37a;
            text-decoration: none;
        }

        /* Citizen portal light DGA color grading */
        .dga-topbar {
            background: #2c6f9e;
        }

        .dga-header {
            background: rgba(255, 255, 255, .98);
            border-bottom-color: var(--dga-line);
            box-shadow: 0 10px 28px rgba(32, 54, 74, .08);
        }

        .dga-brand h1,
        .dga-nav-link,
        .dga-panel h4,
        .dga-section-title span {
            color: var(--dga-blue);
        }

        .dga-brand span,
        .dga-section-title h3,
        .dga-home-body .dga-flow-org-caption p,
        .dga-home-body .dga-wing-link-card p,
        .dga-home-body .dga-roadmap-link-card p,
        .dga-update-frame-note {
            color: var(--dga-muted);
        }

        .dga-nav-link,
        .dga-nav-link:hover {
            border-color: #c8e0ef;
            background: #f7fcff;
        }

        .dga-nav-link.primary,
        .dga-nav-link.primary:hover {
            border-color: var(--dga-blue);
            background: var(--dga-blue);
            color: #fff;
        }

        .dga-department-strip {
            background: #2f75b5;
            border-top: 1px solid #d5e6f1;
            box-shadow: 0 8px 20px rgba(47, 117, 181, .16);
        }

        .dga-bottom-updates-head,
        .dga-home-footer-strip {
            background: linear-gradient(90deg, #2f75b5 0%, #3f9fba 100%);
        }

        .dga-home-footer-strip::before {
            background: linear-gradient(90deg, transparent, rgba(240, 184, 79, .95), rgba(255, 255, 255, .9), transparent);
        }

        .dga-department-strip::after {
            background: rgba(255, 255, 255, .82);
        }

        .dga-department-title,
        .dga-department-title:hover,
        .dga-department-title:focus {
            border-color: #c8e0ef;
            border-radius: 8px;
            background: #eaf6ff;
            color: #174f82;
            box-shadow: 0 6px 14px rgba(22, 75, 120, .12);
        }

        .dga-department-title::before {
            background: #f0b84f;
        }

        .dga-department-title:hover,
        .dga-department-title:focus,
        .dga-department-item.is-open .dga-department-title {
            background: #eaf6ff;
            border-color: #f0b84f;
            color: #174f82;
            box-shadow: 0 8px 18px rgba(22, 75, 120, .18);
        }

        .dga-department-arrow {
            color: #2f75b5;
        }

        .dga-department-item:hover .dga-department-arrow,
        .dga-department-item:focus-within .dga-department-arrow,
        .dga-department-item.is-open .dga-department-arrow {
            color: #c08420;
        }

        .dga-department-menu {
            border-color: #c8e0ef;
            background: #ffffff;
        }

        .dga-department-menu a {
            margin-bottom: 6px;
            background: #eaf6ff;
            color: #174f82;
        }

        .dga-department-menu a:last-child {
            margin-bottom: 0;
        }

        .dga-department-menu a:hover,
        .dga-department-menu a:focus {
            background: #eaf6ff;
            color: #174f82;
        }

        .dga-home-body {
            background: #f4fbff;
        }

        .dga-home-body .dga-page-sections-band {
            background:
                linear-gradient(115deg, #dff1fb 0%, #edf9f5 42%, #ffffff 42%, #f5fbff 100%);
        }

        .dga-home-body .dga-hero-menu-card,
        .dga-home-body .dga-hero-content-shell,
        .dga-bottom-updates,
        .dga-bottom-updates-center,
        .dga-update-card,
        .dga-home-body .dga-flow-orgmap,
        .dga-home-body .dga-wing-link-card,
        .dga-home-body .dga-roadmap-link-card {
            border-color: var(--dga-line);
            background: var(--dga-surface);
            box-shadow: 0 14px 34px var(--dga-shadow);
        }

        .dga-home-body .dga-hero-menu-card {
            background: linear-gradient(180deg, #ffffff, #f0f8ff);
        }

        .dga-home-body .dga-hero-menu-card::before {
            background: var(--dga-blue);
            box-shadow: none;
        }

        .dga-home-body .dga-hero-menu-card::after,
        .dga-home-panel::before,
        .dga-overview-panel::before,
        .dga-home-panel .dga-stat::after {
            display: none;
        }

        .dga-menu-kicker,
        .dga-menu-subtitle,
        .dga-home-intro .dga-eyebrow,
        .dga-home-panel .dga-eyebrow,
        .dga-update-frame-label {
            border-color: rgba(47, 117, 181, .2);
            background: var(--dga-soft-gold);
            color: #775719;
            box-shadow: none;
        }

        .dga-menu-kicker::before {
            background: var(--dga-gold);
            box-shadow: 0 0 0 4px rgba(240, 184, 79, .18);
        }

        .dga-menu-subtitle {
            border-left-color: var(--dga-teal);
        }

        .dga-home-body .dga-hero-menu-title,
        .dga-home-intro h2,
        .dga-home-panel .dga-home-intro h2,
        .dga-overview-panel .dga-hero-info-card h4,
        .dga-home-body .dga-hero-panel h3,
        .dga-home-body .dga-flow-org-caption span,
        .dga-home-body .dga-wing-link-card h4,
        .dga-home-body .dga-roadmap-link-card h4,
        .dga-update-card p {
            color: var(--dga-ink);
        }

        .dga-home-body .dga-hero-menu-title::after,
        .dga-home-body .dga-flow-org-caption span::after {
            background: linear-gradient(90deg, var(--dga-blue), var(--dga-teal));
        }

        .dga-home-body .dga-hero-menu-button,
        .dga-home-body .dga-hero-menu-button:nth-child(n) {
            --dga-menu-dark: #2f75b5;
            --dga-menu-light: #62a9df;
            --dga-menu-accent: #2f75b5;
            --dga-menu-active-gradient: linear-gradient(135deg, #2f75b5, #62a9df);
            --dga-menu-hover-gradient: linear-gradient(135deg, #edf7ff, #b7dcf4);
            --dga-menu-text: #174f82;
            --dga-menu-border: #c8e0ef;
            --dga-menu-icon-bg: rgba(47, 117, 181, .1);
            --dga-menu-shadow: rgba(47, 117, 181, .24);
            border-color: var(--dga-menu-border);
            background: #ffffff;
            color: var(--dga-menu-text);
            transform-origin: center left;
            transition: transform .24s cubic-bezier(.2, .8, .2, 1), border-color .2s ease, background .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .dga-home-body .dga-hero-menu-button::after {
            background: var(--dga-menu-hover-gradient);
            transition: width .34s ease, opacity .22s ease;
        }

        .dga-home-body .dga-hero-menu-button:hover,
        .dga-home-body .dga-hero-menu-button:focus {
            transform: translateX(3px) scale(1.035);
            background: #ffffff;
            border-color: #3c8ccc;
            color: #174f82;
            box-shadow: 0 18px 32px rgba(47, 117, 181, .24);
        }

        .dga-home-body .dga-hero-menu-button:hover::after,
        .dga-home-body .dga-hero-menu-button:focus::after {
            background: var(--dga-menu-hover-gradient);
        }

        .dga-home-body .dga-hero-menu-button.is-active {
            transform: translateX(3px) scale(1.025);
            background: #ffffff;
            border-color: #2f75b5;
            color: #fff;
            box-shadow: 0 16px 30px rgba(47, 117, 181, .24);
        }

        .dga-home-body .dga-hero-menu-button.is-active:hover,
        .dga-home-body .dga-hero-menu-button.is-active:focus {
            transform: translateX(4px) scale(1.045);
            box-shadow: 0 20px 36px rgba(47, 117, 181, .28);
        }

        .dga-home-body .dga-hero-menu-button.is-active::after {
            background: var(--dga-menu-active-gradient);
        }

        .dga-home-body .dga-hero-menu-button:hover i,
        .dga-home-body .dga-hero-menu-button:focus i {
            background: rgba(47, 117, 181, .18);
            color: #174f82;
        }

        .dga-home-body .dga-hero-menu-button.is-active i {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        .dga-home-intro,
        .dga-home-panel .dga-home-intro,
        .dga-home-panel .dga-stat-grid,
        .dga-overview-panel {
            background: linear-gradient(180deg, #ffffff, #f4fbff);
            border-color: var(--dga-line);
        }

        .dga-home-panel .dga-home-intro {
            border-left-color: var(--dga-teal);
        }

        .dga-home-panel .dga-stat,
        .dga-home-body .dga-stat,
        .dga-home-body .dga-hero-info-card,
        .dga-home-body .dga-hero-mini-step {
            border-color: var(--dga-line);
            background: #fff;
            box-shadow: 0 12px 28px rgba(32, 54, 74, .07);
        }

        .dga-home-panel .dga-stat:nth-child(n) {
            --dga-home-stat-accent: var(--dga-blue);
            --dga-home-stat-soft: rgba(47, 117, 181, .08);
        }

        .dga-home-panel .dga-stat:nth-child(2n) {
            --dga-home-stat-accent: var(--dga-teal);
            --dga-home-stat-soft: rgba(72, 169, 154, .09);
        }

        .dga-home-panel .dga-stat:nth-child(3n) {
            --dga-home-stat-accent: #c08420;
            --dga-home-stat-soft: rgba(240, 184, 79, .14);
        }

        .dga-home-body .dga-stat strong,
        .dga-home-body .dga-hero-info-card h4,
        .dga-home-body .dga-hero-mini-step h4 {
            color: var(--dga-blue);
        }

        .dga-home-body .dga-home-panel {
            isolation: isolate;
            background:
                linear-gradient(180deg, #f8fcff 0%, #ffffff 45%, #eef8ff 100%);
        }

        .dga-home-body .dga-home-panel::before {
            display: block;
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                linear-gradient(135deg, rgba(47, 117, 181, .08) 0 18%, transparent 18% 100%),
                radial-gradient(circle at 88% 16%, rgba(240, 184, 79, .22), transparent 0 15%, transparent 27%),
                linear-gradient(90deg, rgba(95, 167, 121, .08), transparent 44%);
            pointer-events: none;
        }

        .dga-home-body .dga-home-panel::after {
            display: none;
            content: none;
        }

        .dga-home-body .dga-home-panel .dga-home-intro {
            min-height: 56%;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, .18);
            border-left: 0;
            background:
                linear-gradient(135deg, rgba(18, 62, 105, .98) 0%, rgba(47, 117, 181, .96) 58%, rgba(40, 137, 144, .92) 100%);
            color: #fff;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .18);
        }

        .dga-home-body .dga-home-panel .dga-home-intro::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 7px;
            background: linear-gradient(90deg, #f0b84f 0 28%, #ffffff 28% 66%, #5fa779 66% 100%);
        }

        .dga-home-body .dga-home-panel .dga-eyebrow {
            border-color: rgba(255, 255, 255, .4);
            background: rgba(255, 255, 255, .14);
            color: #fff;
            box-shadow: none;
        }

        .dga-home-body .dga-home-panel .dga-home-intro h2 {
            max-width: 880px;
            color: #fff;
            text-shadow: 0 14px 26px rgba(8, 35, 64, .28);
        }

        .dga-home-body .dga-home-panel .dga-home-intro p {
            max-width: 820px;
            color: rgba(255, 255, 255, .9);
        }

        .dga-home-body .dga-home-panel .dga-stat-grid {
            position: relative;
            z-index: 2;
            padding: clamp(20px, 2.4vw, 34px);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(241, 248, 252, .98));
        }

        .dga-home-body .dga-home-panel .dga-stat {
            min-height: 150px;
            border: 1px solid rgba(203, 222, 235, .98);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #ffffff, #f7fbff);
            box-shadow: 0 18px 40px rgba(32, 54, 74, .12);
        }

        .dga-home-body .dga-home-panel .dga-stat::before {
            height: 8px;
            background: linear-gradient(90deg, var(--dga-home-stat-accent), rgba(255, 255, 255, .3));
        }

        .dga-home-body .dga-home-panel .dga-stat::after {
            display: block;
            right: -34px;
            bottom: -34px;
            width: 112px;
            height: 112px;
            background: var(--dga-home-stat-soft, rgba(47, 117, 181, .1));
        }

        .dga-home-body .dga-home-panel .dga-stat:hover {
            transform: translateY(-5px) scale(1.015);
            box-shadow: 0 26px 56px rgba(32, 54, 74, .18);
        }

        .dga-home-body .dga-overview-panel {
            isolation: isolate;
            padding: clamp(22px, 2.6vw, 38px);
            background:
                linear-gradient(135deg, #f7fcff 0%, #ffffff 48%, #eef8ff 100%);
        }

        .dga-home-body .dga-overview-panel::before {
            display: block;
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at 8% 12%, rgba(47, 117, 181, .14), transparent 0 18%, transparent 30%),
                radial-gradient(circle at 92% 8%, rgba(240, 184, 79, .2), transparent 0 14%, transparent 28%),
                linear-gradient(135deg, transparent 0 62%, rgba(72, 169, 154, .1) 62% 74%, transparent 74% 100%);
            pointer-events: none;
        }

        .dga-home-body .dga-overview-panel::after {
            content: "";
            position: absolute;
            left: clamp(22px, 3vw, 44px);
            right: clamp(22px, 3vw, 44px);
            top: clamp(84px, 10vw, 128px);
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(47, 117, 181, .24), transparent);
            pointer-events: none;
            z-index: 1;
        }

        .dga-home-body .dga-overview-panel > h3 {
            position: relative;
            z-index: 2;
            min-height: 92px;
            display: flex;
            align-items: center;
            margin: 0 0 clamp(20px, 2vw, 30px);
            padding: 22px 28px 22px 96px;
            overflow: hidden;
            border: 1px solid rgba(200, 224, 239, .98);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff 0%, #edf7ff 100%);
            color: #17395d;
            font-size: clamp(24px, 2.4vw, 34px);
            line-height: 1.15;
            font-weight: 900;
            box-shadow: 0 20px 46px rgba(32, 54, 74, .12);
        }

        .dga-home-body .dga-overview-panel > h3::before {
            left: 26px;
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #174f82, #2f75b5 58%, #62a9df);
            box-shadow: 0 16px 30px rgba(47, 117, 181, .28);
        }

        .dga-home-body .dga-overview-panel > h3::after {
            content: "Briefing";
            position: absolute;
            right: 22px;
            top: 18px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(240, 184, 79, .18);
            color: #8b5d12;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-overview-panel .dga-hero-card-grid {
            position: relative;
            z-index: 2;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(18px, 2.2vw, 28px);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card {
            min-height: 100%;
            padding: 30px 26px 26px 92px;
            border: 1px solid rgba(200, 224, 239, .96);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(247, 252, 255, .96));
            box-shadow: 0 20px 48px rgba(32, 54, 74, .12);
            backdrop-filter: blur(10px);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card::before {
            left: 28px;
            top: 30px;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(47, 117, 181, .14), rgba(72, 169, 154, .18));
            color: #2f75b5;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8), 0 14px 26px rgba(47, 117, 181, .1);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2)::before {
            background: linear-gradient(135deg, rgba(240, 184, 79, .22), rgba(95, 167, 121, .16));
            color: #98670f;
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 6px;
            background: linear-gradient(90deg, #2f75b5, #48a99a);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2)::after {
            background: linear-gradient(90deg, #f0b84f, #5fa779);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:hover {
            transform: translateY(-6px) scale(1.01);
            border-color: rgba(47, 117, 181, .34);
            box-shadow: 0 30px 64px rgba(32, 54, 74, .18);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card h4 {
            margin: 0 0 18px;
            color: #17395d;
            font-size: clamp(21px, 1.8vw, 27px);
            line-height: 1.12;
        }

        .dga-home-body .dga-overview-panel .dga-hero-list {
            counter-reset: overviewPoint;
            gap: 12px;
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li {
            min-height: 62px;
            padding: 13px 14px 13px 52px;
            border: 1px solid rgba(213, 230, 241, .95);
            border-radius: 8px;
            background: rgba(255, 255, 255, .86);
            color: #314b62;
            box-shadow: 0 10px 22px rgba(32, 54, 74, .06);
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li::before {
            counter-increment: overviewPoint;
            content: counter(overviewPoint);
            left: 14px;
            top: 50%;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 8px 16px rgba(47, 117, 181, .18);
            transform: translateY(-50%);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2) .dga-hero-list li::before {
            background: linear-gradient(135deg, #c08420, #f0b84f);
            box-shadow: 0 8px 16px rgba(192, 132, 32, .18);
        }

        .dga-overview-panel > h3 {
            background: #fff;
            box-shadow: 0 12px 28px rgba(32, 54, 74, .07);
        }

        .dga-overview-panel > h3::before,
        .dga-home-body .dga-flow-center-node,
        .dga-home-body .dga-hero-flow-root,
        .dga-home-body .dga-flow-org-root {
            background: linear-gradient(180deg, #3c8ccc, var(--dga-blue));
            color: #fff;
            box-shadow: 0 14px 30px rgba(47, 117, 181, .2);
        }

        .dga-home-body .dga-flow-org-head,
        .dga-home-body .dga-flow-org-column.is-hq .dga-flow-org-head,
        .dga-home-body .dga-flow-org-column.is-field .dga-flow-org-head {
            background: linear-gradient(180deg, #fff8e5, #ffefc1);
            color: var(--dga-ink);
            box-shadow: inset 0 -1px 0 rgba(192, 132, 32, .12);
        }

        .dga-home-body .dga-flow-org-head::before {
            color: var(--dga-blue);
        }

        .dga-home-body .dga-flow-box.is-blue,
        .dga-home-body .dga-flow-subbranch:first-child > .dga-flow-box.is-blue,
        .dga-home-body .dga-flow-subbranch:last-child > .dga-flow-box.is-blue,
        .dga-home-body .dga-flow-box.is-link {
            background: linear-gradient(180deg, #3c8ccc, var(--dga-blue));
            color: #fff;
        }

        .dga-home-body .dga-flow-box.is-green,
        .dga-home-body .dga-flow-subbranch:first-child > .dga-flow-child {
            background: linear-gradient(180deg, #62c3b4, var(--dga-teal));
            color: #fff;
        }

        .dga-home-body .dga-flow-child::before,
        .dga-home-body .dga-flow-division-list::before,
        .dga-home-body .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body .dga-flow-field-list::before,
        .dga-home-body .dga-flow-field-list .dga-flow-box::before,
        .dga-home-body .dga-flow-direction-band::before,
        .dga-home-body .dga-flow-direction-band span::before,
        .dga-home-body .dga-flow-org-root::after,
        .dga-home-body .dga-flow-org-column::before,
        .dga-home-body .dga-flow-org-subgrid::before,
        .dga-home-body .dga-flow-subbranch::before {
            background: #bfd8e8;
        }

        .dga-home-body #dga-panel-flow {
            background:
                linear-gradient(180deg, #f8fcff 0%, #eef7ff 100%);
        }

        .dga-home-body #dga-panel-flow > h3 {
            position: relative;
            margin: 0 0 18px;
            padding: 18px 22px 18px 72px;
            border: 1px solid rgba(200, 224, 239, .98);
            border-radius: 8px;
            background: linear-gradient(135deg, #ffffff, #edf7ff);
            color: #17395d;
            font-size: clamp(22px, 2vw, 30px);
            font-weight: 900;
            box-shadow: 0 16px 34px rgba(32, 54, 74, .09);
        }

        .dga-home-body #dga-panel-flow > h3::before {
            content: "\f0e8";
            position: absolute;
            left: 20px;
            top: 50%;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            transform: translateY(-50%);
            box-shadow: 0 12px 22px rgba(47, 117, 181, .22);
        }

        .dga-home-body #dga-panel-flow .dga-flow-orgmap {
            border: 1px solid rgba(151, 194, 219, .72);
            background:
                linear-gradient(160deg, rgba(11, 50, 84, .96), rgba(24, 86, 125, .94) 50%, rgba(32, 128, 132, .9)),
                radial-gradient(circle at top right, rgba(240, 184, 79, .22), transparent 34%);
            color: #fff;
            box-shadow: 0 26px 62px rgba(18, 53, 84, .22);
        }

        .dga-home-body #dga-panel-flow .dga-flow-orgmap::before {
            display: block;
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .08) 0 1px, transparent 1px 28px),
                radial-gradient(circle at 14% 16%, rgba(255, 255, 255, .16), transparent 0 10%, transparent 24%);
            background-size: 28px 28px, auto;
            pointer-events: none;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-caption {
            border-bottom-color: rgba(255, 255, 255, .18);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-caption span {
            color: #fff;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-caption span::after {
            background: linear-gradient(90deg, #f0b84f, #9be0d5);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-caption p,
        .dga-home-body #dga-panel-flow .dga-flow-column-note {
            color: rgba(255, 255, 255, .78);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-root {
            border: 1px solid rgba(255, 255, 255, .2);
            background: linear-gradient(135deg, #fff7d6, #f0b84f);
            color: #17395d;
            box-shadow: 0 18px 36px rgba(3, 24, 43, .28);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-column.is-hq .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-column.is-field .dga-flow-org-head {
            background: rgba(255, 255, 255, .92);
            color: #17395d;
            box-shadow: 0 12px 26px rgba(3, 24, 43, .16);
        }

        .dga-home-body #dga-panel-flow .dga-flow-box {
            border: 1px solid rgba(255, 255, 255, .18);
            box-shadow: 0 12px 24px rgba(3, 24, 43, .18);
        }

        .dga-home-body #dga-panel-flow .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch:first-child > .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch:last-child > .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link {
            background: linear-gradient(135deg, #eaf6ff, #b8ddf4);
            color: #17395d;
        }

        .dga-home-body #dga-panel-flow .dga-flow-box.is-green,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch:first-child > .dga-flow-child {
            background: linear-gradient(135deg, #e8fbf7, #9de0d5);
            color: #164e49;
        }

        .dga-home-body #dga-panel-flow .dga-flow-box.is-link {
            min-height: 90px;
            background: linear-gradient(135deg, rgba(255, 255, 255, .95), rgba(232, 246, 255, .92));
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link {
            min-height: 74px;
            align-items: center;
            padding: 12px 14px 12px 16px;
            border: 1px solid rgba(255, 255, 255, .46);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(229, 244, 255, .94));
            color: #17395d;
            box-shadow: 0 12px 24px rgba(3, 24, 43, .16);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            border-radius: 8px 0 0 8px;
            background: linear-gradient(180deg, #f0b84f, #2f75b5);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::after {
            content: "Redirect";
            position: absolute;
            right: 48px;
            top: 10px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(47, 117, 181, .1);
            color: #174f82;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:hover,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:focus {
            transform: translateY(-3px) scale(1.01);
            background: linear-gradient(135deg, #f0b84f, #fff1bf);
            color: #17395d;
            box-shadow: 0 20px 38px rgba(3, 24, 43, .24);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus {
            transform: translateX(4px) scale(1.015);
            background:
                linear-gradient(135deg, #174f82, #2f75b5 58%, #62a9df);
            color: #fff;
            box-shadow: 0 20px 40px rgba(3, 24, 43, .3);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover::after,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus::after {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .dga-home-body #dga-panel-flow .dga-flow-box-copy strong {
            color: inherit;
        }

        .dga-home-body #dga-panel-flow .dga-flow-box-copy small {
            color: rgba(23, 57, 93, .78);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box-copy {
            gap: 5px;
            padding-right: 8px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box-copy strong {
            max-width: 100%;
            font-size: 14px;
            line-height: 1.25;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box-copy small:not(.dga-flow-mini-tag) {
            max-width: 100%;
            color: rgba(23, 57, 93, .66);
            font-size: 11px;
            line-height: 1.35;
        }

        .dga-home-body #dga-panel-flow .dga-flow-link-action {
            display: inline-flex;
            align-self: flex-start;
            color: #2f75b5;
            font-size: 11px;
            font-style: normal;
            font-weight: 900;
            line-height: 1;
            text-transform: uppercase;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover .dga-flow-box-copy small,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus .dga-flow-box-copy small,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover .dga-flow-link-action,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus .dga-flow-link-action {
            color: rgba(255, 255, 255, .88);
        }

        .dga-home-body #dga-panel-flow .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link i {
            background: rgba(47, 117, 181, .12);
            color: #174f82;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-mini-tag {
            padding: 3px 8px;
            background: rgba(240, 184, 79, .2);
            color: #8b5d12;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover i,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus i {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .dga-home-body #dga-panel-flow .dga-flow-child::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box::before,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band::before,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-root::after,
        .dga-home-body #dga-panel-flow .dga-flow-org-column::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before {
            background: rgba(255, 255, 255, .48);
            box-shadow: 0 0 12px rgba(255, 255, 255, .18);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-root::after,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band::before,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-column::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before,
        .dga-home-body #dga-panel-flow .dga-flow-child::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box::before {
            background: #9fc3da;
            box-shadow: none;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-root::after {
            height: 34px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-direction-band {
            width: calc(100% - 140px);
            max-width: 720px;
            height: 56px;
            margin: 0 auto -10px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-direction-band::before {
            left: 25%;
            right: 25%;
            top: 34px;
            height: 2px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-direction-band span {
            height: 56px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::before {
            top: 34px;
            height: 22px;
            width: 2px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -5px;
            width: 10px;
            height: 10px;
            border: 2px solid #d9edf8;
            border-radius: 50%;
            background: #2f75b5;
            transform: translateX(-50%);
            box-shadow: 0 0 0 4px rgba(47, 117, 181, .1);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-split {
            gap: 34px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-column::before {
            top: -46px;
            height: 46px;
            width: 2px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-head {
            position: relative;
            z-index: 2;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid {
            padding-top: 22px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before {
            top: 8px;
            height: 2px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-subbranch {
            padding-top: 22px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before {
            height: 22px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list::before {
            top: -14px;
            bottom: 34px;
        }

        .dga-home-body .dga-wing-connection-grid::before,
        .dga-home-body .dga-roadmap-network::before {
            background: linear-gradient(180deg, var(--dga-blue), var(--dga-teal), var(--dga-gold));
            box-shadow: 0 0 0 8px rgba(213, 230, 241, .55);
        }

        .dga-home-body .dga-wing-link-card::after,
        .dga-home-body .dga-wing-link-card:nth-child(n)::after,
        .dga-home-body .dga-roadmap-link-card::after,
        .dga-home-body .dga-roadmap-link-card:nth-child(n)::after {
            background: linear-gradient(180deg, #3c8ccc, var(--dga-blue));
            box-shadow: 0 6px 14px rgba(47, 117, 181, .16);
        }

        .dga-home-body .dga-wing-link-card:nth-child(2n)::after,
        .dga-home-body .dga-roadmap-link-card:nth-child(2n)::after {
            background: linear-gradient(180deg, #62c3b4, var(--dga-teal));
            box-shadow: 0 6px 14px rgba(72, 169, 154, .16);
        }

        .dga-home-body .dga-wing-icon,
        .dga-home-body .dga-wing-link-card:nth-child(n) .dga-wing-icon,
        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step,
        .dga-home-body .dga-roadmap-link-card:nth-child(n) .dga-roadmap-step {
            background: var(--dga-soft-blue);
            color: var(--dga-blue);
        }

        .dga-home-body .dga-wing-link-card:nth-child(2n) .dga-wing-icon,
        .dga-home-body .dga-roadmap-link-card:nth-child(2n) .dga-roadmap-step {
            background: var(--dga-soft-teal);
            color: #25756b;
        }

        .dga-home-body .dga-wing-link-card:nth-child(3n) .dga-wing-icon {
            background: var(--dga-soft-gold);
            color: #a36f17;
        }

        .dga-home-body #dga-panel-wings {
            background:
                linear-gradient(180deg, #f8fcff 0%, #ffffff 50%, #eef8ff 100%);
        }

        .dga-home-body #dga-panel-wings > h3 {
            position: relative;
            margin: 0 0 20px;
            padding: 18px 22px 18px 72px;
            border: 1px solid rgba(200, 224, 239, .98);
            border-radius: 8px;
            background: linear-gradient(135deg, #ffffff, #edf7ff);
            color: #17395d;
            font-size: clamp(22px, 2vw, 30px);
            font-weight: 900;
            box-shadow: 0 16px 34px rgba(32, 54, 74, .09);
        }

        .dga-home-body #dga-panel-wings > h3::before {
            content: "\f279";
            position: absolute;
            left: 20px;
            top: 50%;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #2f75b5, #48a99a);
            color: #fff;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            transform: translateY(-50%);
            box-shadow: 0 12px 22px rgba(47, 117, 181, .22);
        }

        .dga-home-body #dga-panel-wings .dga-wing-network,
        .dga-function-roadmap-col .dga-wing-network {
            position: relative;
            padding: 106px 22px 24px;
            border: 1px solid rgba(200, 224, 239, .86);
            border-radius: 8px;
            background:
                radial-gradient(circle at 12% 8%, rgba(47, 117, 181, .12), transparent 0 16%, transparent 32%),
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(239, 248, 255, .96));
            box-shadow: 0 18px 42px rgba(32, 54, 74, .1);
            overflow: hidden;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::before,
        .dga-function-roadmap-col .dga-wing-network::before {
            content: "DGA Functional Map";
            position: absolute;
            left: 50%;
            top: 22px;
            z-index: 2;
            min-width: 230px;
            padding: 14px 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #174f82, #2f75b5 58%, #48a99a);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .08em;
            text-align: center;
            text-transform: uppercase;
            box-shadow: 0 18px 34px rgba(47, 117, 181, .24);
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::after,
        .dga-function-roadmap-col .dga-wing-network::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 72px;
            width: 3px;
            height: 34px;
            border-radius: 999px;
            background: linear-gradient(180deg, #2f75b5, rgba(47, 117, 181, .16));
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid,
        .dga-function-roadmap-col .dga-wing-connection-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 32px 20px;
            padding: 34px 0 4px;
            counter-reset: wingMap;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before,
        .dga-function-roadmap-col .dga-wing-connection-grid::before {
            left: 10%;
            right: 10%;
            top: 0;
            bottom: auto;
            width: auto;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, #2f75b5 20%, #48a99a 50%, #f0b84f 80%, transparent);
            box-shadow: 0 0 0 7px rgba(213, 230, 241, .38);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card,
        .dga-function-roadmap-col .dga-wing-link-card {
            min-height: 112px;
            padding: 18px 20px 18px 84px;
            overflow: hidden;
            border: 1px solid rgba(200, 224, 239, .98);
            border-radius: 8px;
            background: linear-gradient(135deg, #ffffff, #f6fbff);
            box-shadow: 0 14px 30px rgba(32, 54, 74, .09);
            overflow: visible;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::before,
        .dga-function-roadmap-col .dga-wing-link-card::before {
            left: 50%;
            top: -32px;
            width: 3px;
            height: 32px;
            border-radius: 999px;
            background: rgba(47, 117, 181, .28);
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::after,
        .dga-function-roadmap-col .dga-wing-link-card::after {
            counter-increment: wingMap;
            content: counter(wingMap);
            left: 50%;
            top: -45px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 0 0 6px rgba(47, 117, 181, .12), 0 8px 18px rgba(47, 117, 181, .22);
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(2n)::after,
        .dga-function-roadmap-col .dga-wing-link-card:nth-child(2n)::after {
            background: linear-gradient(135deg, #218879, #5dc7b7);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(3n)::after,
        .dga-function-roadmap-col .dga-wing-link-card:nth-child(3n)::after {
            background: linear-gradient(135deg, #c08420, #f0b84f);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(4n)::after,
        .dga-function-roadmap-col .dga-wing-link-card:nth-child(4n)::after {
            background: linear-gradient(135deg, #565db8, #9096ea);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5n)::after,
        .dga-function-roadmap-col .dga-wing-link-card:nth-child(5n)::after {
            background: linear-gradient(135deg, #c45f42, #ee967a);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:hover,
        .dga-function-roadmap-col .dga-wing-link-card:hover {
            transform: translateX(5px) scale(1.01);
            border-color: rgba(47, 117, 181, .34);
            box-shadow: 0 24px 50px rgba(32, 54, 74, .16);
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon,
        .dga-function-roadmap-col .dga-wing-icon {
            left: 20px;
            top: 20px;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8), 0 12px 22px rgba(47, 117, 181, .12);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card h4,
        .dga-function-roadmap-col .dga-wing-link-card h4 {
            margin-bottom: 9px;
            color: #17395d;
            font-size: 17px;
            line-height: 1.25;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card p,
        .dga-function-roadmap-col .dga-wing-link-card p {
            color: #53697b;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 575px) {
            .dga-home-body #dga-panel-wings .dga-wing-network,
            .dga-function-roadmap-col .dga-wing-network {
                padding: 96px 14px 18px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-network::before,
            .dga-function-roadmap-col .dga-wing-network::before {
                min-width: 0;
                width: calc(100% - 32px);
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid,
            .dga-function-roadmap-col .dga-wing-connection-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        .dga-home-body #dga-panel-wings .dga-wing-network,
        .dga-function-roadmap-col .dga-wing-network {
            padding: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(238, 248, 255, .96)),
                radial-gradient(circle at 50% 14%, rgba(47, 117, 181, .13), transparent 30%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::before,
        .dga-home-body #dga-panel-wings .dga-wing-network::after,
        .dga-function-roadmap-col .dga-wing-network::before,
        .dga-function-roadmap-col .dga-wing-network::after {
            content: none;
        }

        .dga-wing-map-hub {
            position: relative;
            z-index: 2;
            width: min(100%, 340px);
            min-height: 86px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 0 auto 54px;
            border: 1px solid rgba(255, 255, 255, .34);
            border-radius: 8px;
            background: linear-gradient(135deg, #174f82, #2f75b5 58%, #48a99a);
            color: #fff;
            text-align: center;
            box-shadow: 0 20px 42px rgba(47, 117, 181, .26);
        }

        .dga-wing-map-hub::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 100%;
            width: 3px;
            height: 54px;
            border-radius: 999px;
            background: linear-gradient(180deg, #2f75b5, rgba(47, 117, 181, .18));
            transform: translateX(-50%);
        }

        .dga-wing-map-hub span {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
            opacity: .82;
        }

        .dga-wing-map-hub strong {
            font-size: clamp(22px, 2vw, 30px);
            font-weight: 900;
            line-height: 1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid,
        .dga-function-roadmap-col .dga-wing-connection-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 42px 24px;
            padding: 0;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before,
        .dga-function-roadmap-col .dga-wing-connection-grid::before {
            left: 25%;
            right: 25%;
            top: -28px;
            height: 3px;
            background: linear-gradient(90deg, #2f75b5, #48a99a, #f0b84f);
            box-shadow: 0 0 0 7px rgba(213, 230, 241, .42);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card,
        .dga-function-roadmap-col .dga-wing-link-card {
            min-height: 146px;
            padding: 74px 20px 20px;
            text-align: center;
            overflow: visible;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5),
        .dga-function-roadmap-col .dga-wing-link-card:nth-child(5) {
            grid-column: 1 / -1;
            width: min(100%, 420px);
            justify-self: center;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::before,
        .dga-function-roadmap-col .dga-wing-link-card::before {
            left: 50%;
            top: -42px;
            width: 3px;
            height: 42px;
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::after,
        .dga-function-roadmap-col .dga-wing-link-card::after {
            top: -58px;
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon,
        .dga-function-roadmap-col .dga-wing-icon {
            left: 50%;
            top: 18px;
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-home.dga-home-panel {
            min-height: clamp(255px, 31vh, 335px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(18px, 2.3vw, 30px) !important;
            overflow: hidden;
            isolation: isolate;
            background:
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, .7), transparent 0 32%, transparent 54%),
                linear-gradient(115deg, #f8fcff 0%, #eef8fb 48%, #e6f2f5 100%);
        }

        .dga-home-body #dga-panel-home.dga-home-panel:not(.is-active) {
            display: none;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::before,
        .dga-home-body #dga-panel-home.dga-home-panel::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::before {
            left: 0;
            width: 52%;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .14), rgba(248, 252, 255, .76)),
                url("{{ asset('site/image/background.jpg') }}") left center / cover no-repeat;
            clip-path: none;
            opacity: .7;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::after {
            right: 0;
            width: 60%;
            background:
                linear-gradient(270deg, rgba(7, 45, 78, .36) 0%, rgba(37, 111, 153, .2) 42%, rgba(248, 252, 255, .54) 100%),
                url("{{ asset('site/image/hero-bg.png') }}") right center / cover no-repeat;
            clip-path: none;
            opacity: 1;
        }

        .dga-home-body #dga-panel-home .dga-home-intro {
            position: relative;
            z-index: 1;
            width: min(100%, 720px);
            min-height: 0;
            margin-left: clamp(14px, 2.5vw, 36px);
            padding: clamp(22px, 3vw, 38px) clamp(24px, 3.4vw, 44px);
            border: 1px solid rgba(204, 223, 237, .9);
            border-left: 6px solid var(--dga-theme-primary);
            border-radius: 10px;
            background: rgba(255, 255, 255, .9);
            box-shadow: 0 20px 48px rgba(20, 74, 111, .16);
            backdrop-filter: blur(7px);
        }

        .dga-home-body #dga-panel-home .dga-home-intro::before {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro::after,
        .dga-home-body #dga-panel-home .dga-stat-grid {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro h2 {
            max-width: 690px;
            margin-bottom: 10px;
            color: #0e4775;
            font-size: clamp(21px, 1.95vw, 30px);
            line-height: 1.18;
            text-shadow: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro p {
            max-width: 680px;
            color: #2f4960;
            font-size: clamp(12px, .95vw, 14px);
            line-height: 1.55;
        }

        @media (max-width: 991px) {
            .dga-home-body #dga-panel-wings .dga-wing-connection-grid,
            .dga-function-roadmap-col .dga-wing-connection-grid {
                grid-template-columns: 1fr;
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before,
            .dga-function-roadmap-col .dga-wing-connection-grid::before {
                left: 50%;
                right: auto;
                width: 3px;
                height: calc(100% + 28px);
                background: linear-gradient(180deg, #2f75b5, #48a99a, #f0b84f);
            }

            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5),
            .dga-function-roadmap-col .dga-wing-link-card:nth-child(5) {
                width: 100%;
            }
        }

        .dga-home-body .dga-roadmap-link-card,
        .dga-home-body .dga-roadmap-link-card:nth-child(even) {
            background: linear-gradient(180deg, #ffffff, #f6fcff);
        }

        .dga-home-body .dga-roadmap-phase,
        .dga-home-body .dga-roadmap-link-card:nth-child(even) .dga-roadmap-phase {
            background: var(--dga-blue);
            color: #fff;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even) .dga-roadmap-phase {
            background: #c08420;
        }

        .dga-bottom-updates,
        .dga-bottom-updates-window,
        .dga-update-photo-frame {
            background: linear-gradient(180deg, #ffffff, #eef8ff);
        }

        .dga-update-photo-frame {
            border-color: rgba(47, 117, 181, .22);
            color: var(--dga-blue);
        }

        .dga-update-frame-content i {
            background: var(--dga-soft-blue);
            color: var(--dga-blue);
            box-shadow: none;
        }

        .dga-update-date-badge {
            background: var(--dga-soft-gold);
            color: #775719;
            box-shadow: none;
        }

        .dga-home-footer-heading,
        .dga-home-footer-mail:hover,
        .dga-home-footer-mail:focus {
            color: #fff4c7;
        }

        .dga-home-footer-link {
            border-radius: 8px;
        }

        .dga-home-footer-link i,
        .dga-home-footer-heading i,
        .dga-home-footer-mail i,
        .dga-footer-top-link i {
            color: #fff4c7;
        }

        .dga-home-body .dga-home-footer-strip {
            grid-template-columns: minmax(0, 1.08fr) minmax(0, 1.22fr) minmax(0, 1fr);
            gap: 14px;
            margin: 10px 14px 14px;
            padding: 24px 74px 22px 24px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 8px;
            background:
                radial-gradient(circle at 10% 18%, rgba(255, 255, 255, .16), transparent 0 24%, transparent 38%),
                linear-gradient(135deg, #174f82 0%, #2f75b5 52%, #48a99a 100%);
            box-shadow: 0 20px 42px rgba(23, 79, 130, .18);
        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 4px;
            background: linear-gradient(90deg, transparent, #f0b84f, rgba(255, 255, 255, .95), #9be0d5, transparent);
        }

        .dga-home-body .dga-home-footer-brand,
        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            padding: 8px 12px;
        }

        .dga-home-body .dga-home-footer-center {
            text-align: center;
        }

        .dga-home-body .dga-home-footer-heading {
            color: #fff4c7;
        }

        .dga-home-body .dga-home-footer-nav {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px;
        }

        .dga-home-body .dga-home-footer-link {
            justify-content: center;
            min-height: 42px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .18);
        }

        .dga-home-body .dga-home-footer-link:hover,
        .dga-home-body .dga-home-footer-link:focus {
            background: rgba(255, 255, 255, .2);
            border-color: rgba(255, 244, 199, .54);
            transform: translateY(-2px);
        }

        .dga-home-body .dga-home-footer-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dga-home-body .dga-home-footer-mail {
            width: max-content;
            max-width: 100%;
            padding: 10px 12px;
            border: 1px solid rgba(255, 244, 199, .36);
            border-radius: 8px;
            background: rgba(255, 255, 255, .11);
        }

        .dga-home-body .dga-footer-top-link {
            position: absolute;
            right: 18px;
            top: 50%;
            z-index: 3;
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 14px 28px rgba(13, 45, 73, .24);
            transform: translateY(-50%);
            transition: transform .22s ease, background .22s ease, border-color .22s ease, box-shadow .22s ease;
        }

        .dga-home-body .dga-footer-top-link:hover,
        .dga-home-body .dga-footer-top-link:focus {
            background: #fff4c7;
            border-color: #fff4c7;
            color: #174f82;
            text-decoration: none;
            box-shadow: 0 18px 34px rgba(13, 45, 73, .28);
            transform: translateY(-50%) scale(1.08);
        }

        .dga-home-body .dga-footer-top-link:hover i,
        .dga-home-body .dga-footer-top-link:focus i {
            color: #174f82;
        }

        .dga-home-body .dga-home-footer-strip {
            align-items: stretch;
            grid-template-columns: minmax(250px, .95fr) minmax(270px, 1.05fr) minmax(300px, 1.2fr);
            gap: 14px;
            margin: 16px 14px 18px;
            padding: 16px 66px 16px 16px;
            border: 1px solid #c8e0ef;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(245, 251, 255, .96)),
                linear-gradient(90deg, #eaf6ff, #f8fcff);
            box-shadow: 0 16px 34px rgba(47, 117, 181, .12);
            color: #20364a;
        }

        .dga-home-body {
            min-height: 100svh;
            height: auto;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .dga-home-body main {
            min-height: calc(100svh - var(--dga-fixed-top-height, 164px));
            height: auto;
            overflow: visible;
        }

	        .dga-home-body .dga-page-sections-band {
	            min-height: calc(100svh - var(--dga-fixed-top-height, 164px));
	            height: auto;
	            padding-bottom: 20px;
	            overflow: visible;
	        }

        .dga-home-body .dga-page-sections-band .container {
            min-height: 0;
            height: auto;
        }

	        .dga-home-body .dga-hero-section-grid {
	            flex: 0 0 auto;
	            min-height: min(610px, calc(100svh - var(--dga-fixed-top-height, 164px) - 120px));
	        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 5px;
            background: linear-gradient(90deg, #174f82 0 32%, #48a99a 32% 68%, #f0b84f 68% 100%);
        }

        .dga-home-body .dga-home-footer-brand,
        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 124px;
            padding: 18px;
            border: 1px solid rgba(200, 224, 239, .92);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(32, 54, 74, .06);
        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            border-left: 1px solid rgba(200, 224, 239, .92);
        }

        .dga-home-body .dga-home-footer-brand {
            border-left: 5px solid #174f82;
        }

        .dga-home-body .dga-home-footer-brand img {
            width: 58px;
            height: 58px;
            border: 1px solid #c8e0ef;
            border-radius: 8px;
            background: #f8fcff;
        }

        .dga-home-body .dga-home-footer-brand h3 {
            color: #17395d;
            font-size: 19px;
        }

        .dga-home-body .dga-home-footer-brand span,
        .dga-home-body .dga-home-footer-note,
        .dga-home-body .dga-home-footer-meta p {
            color: #5f7282;
        }

        .dga-home-body .dga-home-footer-heading {
            color: #174f82;
            letter-spacing: .04em;
        }

        .dga-home-body .dga-home-footer-heading i,
        .dga-home-body .dga-home-footer-link i,
        .dga-home-body .dga-home-footer-mail i {
            color: #2f75b5;
        }

        .dga-home-body .dga-home-footer-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            border-top: 4px solid #48a99a;
        }

        .dga-home-body .dga-home-footer-mail {
            justify-content: center;
            width: 100%;
            margin: 2px 0 8px;
            padding: 12px 14px;
            border: 1px solid #b7d8eb;
            border-radius: 8px;
            background: linear-gradient(180deg, #f8fcff, #eaf6ff);
            color: #174f82;
            font-size: 15px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9);
        }

        .dga-home-body .dga-home-footer-mail:hover,
        .dga-home-body .dga-home-footer-mail:focus {
            border-color: #2f75b5;
            background: #ffffff;
            color: #174f82;
        }

        .dga-home-body .dga-home-footer-meta {
            border-top: 4px solid #f0b84f;
        }

        .dga-home-body .dga-footer-support-list {
            display: grid;
            gap: 8px;
            margin: 0 0 10px;
            padding: 0;
            list-style: none;
        }

        .dga-home-body .dga-footer-support-list li {
            position: relative;
            min-height: 34px;
            padding: 8px 10px 8px 34px;
            border: 1px solid #d5e6f1;
            border-radius: 8px;
            background: #f8fcff;
            color: #314b62;
            font-size: 13px;
            line-height: 1.35;
        }

        .dga-home-body .dga-footer-support-list li::before {
            content: "";
            position: absolute;
            left: 12px;
            top: 14px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2f75b5;
            box-shadow: 0 0 0 4px rgba(47, 117, 181, .1);
        }

        .dga-home-body .dga-home-footer-nav {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .dga-home-body .dga-home-footer-link {
            justify-content: flex-start;
            min-height: 38px;
            padding: 9px 10px;
            border: 1px solid #d5e6f1;
            border-radius: 8px;
            background: #f8fcff;
            color: #174f82;
            box-shadow: none;
        }

        .dga-home-body .dga-home-footer-link:hover,
        .dga-home-body .dga-home-footer-link:focus {
            border-color: #2f75b5;
            background: #eaf6ff;
            color: #174f82;
            transform: translateY(-1px);
        }

        .dga-home-body .dga-footer-top-link {
            right: 16px;
            width: 44px;
            height: 44px;
            border-color: #c8e0ef;
            background: linear-gradient(135deg, #174f82, #2f75b5);
            color: #ffffff;
            box-shadow: 0 12px 26px rgba(47, 117, 181, .24);
        }

        .dga-home-body .dga-footer-top-link i {
            color: #ffffff;
        }

        .dga-home-body .dga-footer-top-link:hover,
        .dga-home-body .dga-footer-top-link:focus {
            background: linear-gradient(135deg, #f0b84f, #fff4c7);
            border-color: #f0b84f;
            color: #174f82;
        }

        .dga-home-body .dga-home-footer-strip {
            grid-template-columns: minmax(280px, .88fr) minmax(420px, 1.12fr);
            width: calc(100% + clamp(10px, 1.2vw, 18px) + clamp(12px, 1.8vw, 26px));
            max-width: none;
            margin: 16px calc(-1 * clamp(12px, 1.8vw, 26px)) 22px calc(-1 * clamp(10px, 1.2vw, 18px));
            padding: 18px 76px 18px 18px;
            border: 0;
            border-radius: 0;
            background:
                radial-gradient(circle at 8% 18%, rgba(255, 255, 255, .18), transparent 0 18%, transparent 34%),
                linear-gradient(135deg, #174f82 0%, #2f75b5 52%, #25756b 100%);
            box-shadow: 0 -1px 0 rgba(255, 255, 255, .72), 0 18px 38px rgba(23, 79, 130, .18);
        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 138px;
            border: 1px solid rgba(255, 255, 255, .48);
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 14px 28px rgba(13, 45, 73, .16);
        }

        .dga-home-body .dga-home-footer-center {
            border-left: 0;
            border-top-color: #9be0d5;
        }

        .dga-home-body .dga-home-footer-meta {
            border-left: 1px solid rgba(255, 255, 255, .48);
            border-top-color: #f0b84f;
        }

        .dga-home-body .dga-home-footer-note {
            color: #466276;
        }

        .dga-home-body .dga-footer-support-list li,
        .dga-home-body .dga-home-footer-mail {
            background: linear-gradient(180deg, #ffffff, #eef8ff);
            border-color: #b7d8eb;
        }

        .dga-home-body .dga-footer-top-link {
            right: 18px;
            background: linear-gradient(135deg, #f0b84f, #fff4c7);
            border-color: rgba(255, 255, 255, .8);
            color: #174f82;
        }

        .dga-home-body .dga-footer-top-link i {
            color: #174f82;
        }

        .dga-home-body .dga-home-footer-strip {
            grid-template-columns: minmax(320px, .82fr) minmax(460px, 1.18fr);
            align-items: stretch;
            gap: 0;
            width: calc(100% + clamp(10px, 1.2vw, 18px) + clamp(12px, 1.8vw, 26px));
            margin: 18px calc(-1 * clamp(12px, 1.8vw, 26px)) 20px calc(-1 * clamp(10px, 1.2vw, 18px));
            padding: 18px 76px 18px 18px;
            border-top: 5px solid #174f82;
            border-bottom: 1px solid #c8e0ef;
            border-radius: 0;
            background:
                linear-gradient(90deg, rgba(240, 184, 79, .18) 0 22%, rgba(72, 169, 154, .14) 22% 58%, rgba(47, 117, 181, .12) 58% 100%),
                linear-gradient(180deg, #ffffff 0%, #eef8ff 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .94), 0 14px 30px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 5px;
            background: linear-gradient(90deg, #f0b84f 0 24%, #48a99a 24% 64%, #2f75b5 64% 100%);
        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 132px;
            padding: 18px 20px;
            border: 1px solid #c8e0ef;
            border-top: 0;
            border-radius: 0;
            background: rgba(255, 255, 255, .9);
            box-shadow: none;
        }

        .dga-home-body .dga-home-footer-center {
            border-left: 5px solid #48a99a;
            border-right: 0;
            text-align: left;
        }

        .dga-home-body .dga-home-footer-meta {
            border-left: 5px solid #f0b84f;
        }

        .dga-home-body .dga-home-footer-heading {
            margin-bottom: 10px;
            color: #17395d;
            font-size: 12px;
            letter-spacing: .06em;
        }

        .dga-home-body .dga-home-footer-heading i {
            color: #c08420;
        }

        .dga-home-body .dga-home-footer-mail {
            justify-content: flex-start;
            width: min(100%, 390px);
            margin: 0 0 8px;
            background: #eef8ff;
            border-color: #b7d8eb;
            color: #174f82;
        }

        .dga-home-body .dga-home-footer-note {
            color: #4d6375;
            font-size: 13px;
        }

        .dga-home-body .dga-footer-support-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .dga-home-body .dga-footer-support-list li {
            min-height: 38px;
            background: #f8fcff;
            border-color: #d5e6f1;
            color: #2d465b;
        }

        .dga-home-body .dga-footer-support-list li::before {
            background: #48a99a;
            box-shadow: 0 0 0 4px rgba(72, 169, 154, .12);
        }

        .dga-home-body .dga-footer-top-link {
            top: 50%;
            right: 18px;
            width: 42px;
            height: 42px;
            background: #174f82;
            border-color: #174f82;
            box-shadow: 0 10px 22px rgba(23, 79, 130, .26);
            transform: translateY(-50%);
        }

        .dga-home-body .dga-footer-top-link i {
            color: #ffffff;
        }

        .dga-home-body .dga-footer-top-link:hover,
        .dga-home-body .dga-footer-top-link:focus {
            background: #f0b84f;
            border-color: #f0b84f;
            color: #17395d;
            transform: translateY(-50%) scale(1.08);
        }

        .dga-home-body .dga-home-footer-strip {
            grid-template-columns: minmax(170px, .36fr) minmax(300px, .78fr) minmax(460px, 1.18fr);
            gap: 12px;
            padding: 18px 76px 18px 18px;
            border-top: 0;
            border-bottom: 0;
            background:
                linear-gradient(90deg, #16384d 0%, #20516a 44%, #2f756f 100%);
            box-shadow: 0 18px 36px rgba(16, 49, 69, .2);
        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 4px;
            background: linear-gradient(90deg, #f0b84f, #9be0d5, #ffffff, #f0b84f);
        }

        .dga-home-body .dga-footer-credit {
            position: relative;
            z-index: 1;
            min-height: 132px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            padding: 18px 16px;
            border-left: 4px solid #f0b84f;
            background: rgba(255, 255, 255, .1);
            color: #ffffff;
        }

        .dga-home-body .dga-footer-credit span {
            color: rgba(255, 255, 255, .78);
            font-size: 12px;
            line-height: 1.45;
            text-transform: uppercase;
        }

        .dga-home-body .dga-footer-credit strong {
            color: #fff4c7;
            font-size: 24px;
            line-height: 1;
        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 132px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-left: 0;
            border-top: 0;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 12px 26px rgba(13, 45, 73, .14);
        }

        .dga-home-body .dga-home-footer-center {
            border-left: 4px solid #9be0d5;
        }

        .dga-home-body .dga-home-footer-meta {
            border-left: 4px solid #f0b84f;
        }

        .dga-home-body .dga-home-footer-heading {
            color: #16384d;
        }

        .dga-home-body .dga-home-footer-heading i {
            color: #2f756f;
        }

        .dga-home-body .dga-home-footer-mail,
        .dga-home-body .dga-footer-support-list li {
            background: #ffffff;
            border-color: #c8e0ef;
        }

        .dga-home-body .dga-footer-top-link {
            background: #fff4c7;
            border-color: rgba(255, 255, 255, .8);
            color: #16384d;
            top: 50%;
            transform: translateY(-50%);
        }

        .dga-home-body .dga-footer-top-link i {
            color: #16384d;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-hero-section-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto minmax(0, 1fr);
                width: 100%;
                max-width: none;
                gap: 12px;
            }

            .dga-home-body .dga-hero-menu-card {
                gap: 8px;
                padding: 12px;
            }

            .dga-home-body .dga-menu-subtitle {
                display: none;
            }

            .dga-home-body .dga-hero-menu {
                display: flex;
                overflow-x: auto;
                overflow-y: hidden;
                gap: 8px;
                padding-bottom: 2px;
            }

            .dga-home-body .dga-hero-menu-button {
                flex: 0 0 auto;
                min-height: 40px;
                white-space: nowrap;
            }

            .dga-home-intro {
                padding: 14px 16px;
            }

            .dga-home-intro h2 {
                font-size: 24px;
            }

            .dga-home-intro p {
                font-size: 14px;
            }

            .dga-home-panel .dga-stat-grid {
                grid-template-columns: 1fr;
            }

            .dga-overview-panel > h3 {
                padding: 18px 18px 18px 72px;
            }

            .dga-overview-panel .dga-hero-info-card {
                padding: 22px 18px 20px 68px;
            }

	            .dga-bottom-updates {
	                grid-template-columns: 1fr;
	                padding: 14px;
	            }

	            .dga-bottom-updates-center {
	                order: 1;
	            }

	            .dga-update-photo-frame {
	                order: 2;
	                min-height: 170px;
	            }

		            .dga-home-footer-strip {
		                grid-template-columns: 1fr;
		                margin: 0;
		                padding: 20px 68px 16px 16px;
		            }

            .dga-home-body .dga-home-footer-strip {
                grid-template-columns: 1fr;
                width: calc(100% + clamp(10px, 1.2vw, 18px) + clamp(12px, 1.8vw, 26px));
                margin: 14px calc(-1 * clamp(12px, 1.8vw, 26px)) 18px calc(-1 * clamp(10px, 1.2vw, 18px));
                padding: 16px 64px 16px 16px;
            }

            .dga-home-body .dga-footer-credit {
                min-height: 0;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 12px 14px;
            }

            .dga-home-body .dga-home-footer-brand,
            .dga-home-body .dga-home-footer-center,
            .dga-home-body .dga-home-footer-meta {
                min-height: 0;
            }

	            .dga-bottom-updates-window {
	                min-height: 0;
	            }

	            .dga-home-footer-center,
	            .dga-home-footer-meta {
	                border-left: 0;
	                border-top: 1px solid rgba(255, 255, 255, .12);
	            }

	            .dga-home-footer-nav {
	                grid-template-columns: repeat(2, minmax(0, 1fr));
	            }

            .dga-home-body .dga-flow-orgmap {
                min-height: 0;
                padding: 18px 14px 14px;
                border-radius: 18px;
            }

            .dga-home-body .dga-flow-org-caption {
                flex-direction: column;
                gap: 8px;
                margin-bottom: 14px;
                grid-template-columns: 1fr;
                padding: 16px;
            }

            .dga-home-body .dga-flow-org-caption p {
                max-width: none;
                text-align: left;
            }

            .dga-home-body .dga-flow-direction-band {
                display: none;
            }

            .dga-home-body .dga-flow-org-root::after,
            .dga-home-body .dga-flow-org-split::before,
            .dga-home-body .dga-flow-org-column::before,
            .dga-home-body .dga-flow-org-subgrid::before,
            .dga-home-body .dga-flow-subbranch::before,
            .dga-home-body .dga-flow-child::before,
            .dga-home-body .dga-flow-division-list::before,
            .dga-home-body .dga-flow-field-list::before,
            .dga-home-body .dga-flow-division-list .dga-flow-box::before,
            .dga-home-body .dga-flow-field-list .dga-flow-box::before {
                display: none;
            }

            .dga-home-body .dga-flow-org-split,
            .dga-home-body .dga-flow-org-subgrid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .dga-home-body .dga-flow-division-list,
            .dga-home-body .dga-flow-field-list {
                grid-template-columns: 1fr;
            }

            .dga-home-body .dga-wing-connection-grid {
                padding-left: 46px;
            }

            .dga-home-body .dga-wing-connection-grid::before {
                left: 18px;
            }

            .dga-home-body .dga-wing-link-card {
                padding: 16px 16px 16px 64px;
            }

            .dga-home-body .dga-wing-link-card::before {
                left: -28px;
                width: 28px;
            }

            .dga-home-body .dga-wing-link-card::after {
                left: -36px;
            }

            .dga-home-body .dga-wing-icon {
                left: 16px;
                width: 38px;
                height: 38px;
                font-size: 16px;
            }

            .dga-home-body .dga-roadmap-network {
                padding-left: 34px;
            }

            .dga-home-body .dga-roadmap-network::before {
                left: 10px;
            }

            .dga-home-body .dga-roadmap-link-card,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd),
            .dga-home-body .dga-roadmap-link-card:nth-child(even) {
                margin-left: 0;
                margin-right: 0;
                padding: 20px 18px 20px 84px;
            }

            .dga-home-body .dga-roadmap-link-card::before {
                left: -22px;
                width: 22px;
            }

            .dga-home-body .dga-roadmap-link-card::after {
                left: -30px;
            }

            .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
                left: 18px;
                width: 48px;
                height: 48px;
                font-size: 18px;
            }

            .dga-home-body .dga-flow-org-split {
                margin-top: 18px;
            }

            .dga-home-body .dga-flow-org-column {
                padding: 14px;
            }

            .dga-home-body .dga-flow-child,
            .dga-home-body .dga-flow-field-list {
                width: 100%;
                margin-top: 14px;
                margin-left: 0;
                padding-left: 0;
            }
        }

        @media (max-width: 575px) {
	            .dga-bottom-updates {
	                padding: 12px;
	            }

	            .dga-update-photo-frame {
	                display: none;
	            }

	            .dga-bottom-updates-head {
	                padding: 12px 14px;
	            }

	            .dga-bottom-updates-window {
	                padding: 12px;
	            }

	            .dga-home-footer-strip {
	                margin: 0 10px 10px;
	                padding: 18px 14px 14px;
	                border-radius: 14px;
	            }

	            .dga-home-footer-brand {
	                align-items: flex-start;
	            }

		            .dga-home-footer-nav {
		                grid-template-columns: 1fr;
		            }

            .dga-home-body .dga-home-footer-strip {
                width: calc(100% + 20px);
                margin: 0 -10px 12px;
                padding: 14px 56px 14px 12px;
                border-radius: 0;
            }

            .dga-home-body .dga-footer-credit {
                align-items: flex-start;
                flex-direction: column;
            }

            .dga-home-body .dga-home-footer-brand,
            .dga-home-body .dga-home-footer-center,
            .dga-home-body .dga-home-footer-meta {
                padding: 14px;
            }

            .dga-home-body .dga-footer-top-link {
                right: 12px;
                width: 38px;
                height: 38px;
            }

		            .dga-home-body .dga-hero-panel {
		                padding: 14px;
	            }

            .dga-home-body .dga-flow-org-root {
                width: 100%;
                font-size: 18px;
            }

            .dga-home-body .dga-flow-org-head {
                font-size: 15px;
            }

            .dga-home-body .dga-flow-box {
                min-height: 52px;
                padding: 10px 12px;
                font-size: 13px;
            }

            .dga-home-body .dga-wing-connection-grid {
                padding-left: 0;
            }

            .dga-home-body .dga-wing-connection-grid::before,
            .dga-home-body .dga-wing-link-card::before,
            .dga-home-body .dga-wing-link-card::after {
                display: none;
            }

            .dga-home-body .dga-wing-link-card {
                padding: 14px 14px 14px 58px;
            }

            .dga-home-body .dga-roadmap-network {
                padding-left: 0;
            }

            .dga-home-body .dga-roadmap-network::before,
            .dga-home-body .dga-roadmap-link-card::before,
            .dga-home-body .dga-roadmap-link-card::after {
                display: none;
            }

            .dga-home-body .dga-roadmap-link-card,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd),
            .dga-home-body .dga-roadmap-link-card:nth-child(even) {
                padding: 18px 14px 18px 68px;
                border-radius: 18px;
            }

            .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
                left: 14px;
                top: 18px;
                width: 42px;
                height: 42px;
                border-radius: 14px;
                font-size: 16px;
            }

            .dga-home-body .dga-roadmap-phase {
                margin-bottom: 10px;
                padding: 5px 12px;
                font-size: 10px;
            }
        }

        .dga-home-body .dga-roadmap-network {
            position: relative;
            margin-top: 18px;
            padding: 28px 18px 34px;
            border: 1px solid rgba(190, 211, 225, .9);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(239, 247, 252, .94)),
                repeating-linear-gradient(90deg, rgba(47, 117, 181, .04) 0 1px, transparent 1px 54px),
                repeating-linear-gradient(0deg, rgba(72, 169, 154, .035) 0 1px, transparent 1px 54px);
            overflow: hidden;
        }

        .dga-home-body .dga-roadmap-network::before {
            content: "";
            position: absolute;
            inset: 18px 8% 18px;
            width: auto;
            border-radius: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 680 920' preserveAspectRatio='none'%3E%3Cpath d='M340 24 C92 112 92 232 340 320 S588 528 340 612 S92 800 340 896' fill='none' stroke='%23cfdce7' stroke-width='58' stroke-linecap='round'/%3E%3Cpath d='M340 24 C92 112 92 232 340 320 S588 528 340 612 S92 800 340 896' fill='none' stroke='%23eef6fb' stroke-width='42' stroke-linecap='round'/%3E%3Cpath d='M340 24 C92 112 92 232 340 320 S588 528 340 612 S92 800 340 896' fill='none' stroke='%232f75b5' stroke-width='5' stroke-linecap='round' stroke-dasharray='18 18' opacity='.72'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 100% 100%;
            box-shadow: none;
            pointer-events: none;
            z-index: 0;
        }

        .dga-home-body .dga-roadmap-network::after {
            content: "Start";
            position: absolute;
            left: 50%;
            top: 16px;
            z-index: 1;
            transform: translateX(-50%);
            padding: 5px 14px;
            border-radius: 999px;
            background: #174f82;
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(47, 117, 181, .18);
        }

        .dga-home-body .dga-roadmap-connection-list {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 38px 74px;
            margin: 0;
            padding: 54px 0 26px;
            list-style: none;
        }

        .dga-home-body .dga-roadmap-connection-list::after {
            content: "Finish";
            position: absolute;
            left: 50%;
            bottom: -8px;
            transform: translateX(-50%);
            padding: 5px 14px;
            border-radius: 999px;
            background: #c08420;
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(192, 132, 32, .18);
        }

        .dga-home-body .dga-roadmap-link-card,
        .dga-home-body .dga-roadmap-link-card:nth-child(odd),
        .dga-home-body .dga-roadmap-link-card:nth-child(even) {
            width: min(100%, 430px);
            min-height: 142px;
            margin: 0;
            padding: 22px 22px 22px 88px;
            border: 1px solid rgba(189, 211, 225, .95);
            border-radius: 8px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .98), rgba(244, 250, 254, .96));
            box-shadow: 0 16px 34px rgba(32, 54, 74, .11);
            overflow: visible;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(odd) {
            justify-self: end;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even) {
            justify-self: start;
            transform: translateY(62px);
        }

        .dga-home-body .dga-roadmap-link-card:hover {
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 24px 46px rgba(32, 54, 74, .17);
            transform: translateY(-4px) scale(1.01);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even):hover {
            transform: translateY(58px) scale(1.01);
        }

        .dga-home-body .dga-roadmap-link-card::before {
            content: "";
            position: absolute;
            top: 50%;
            width: 44px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(47, 117, 181, .2), rgba(47, 117, 181, .8));
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(odd)::before {
            right: -44px;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even)::before {
            left: -44px;
            background: linear-gradient(90deg, rgba(72, 169, 154, .8), rgba(72, 169, 154, .2));
        }

        .dga-home-body .dga-roadmap-link-card::after {
            content: "";
            position: absolute;
            top: calc(50% - 10px);
            width: 20px;
            height: 20px;
            border: 4px solid #fff;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            box-shadow: 0 0 0 6px rgba(47, 117, 181, .12), 0 10px 20px rgba(47, 117, 181, .2);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(odd)::after {
            right: -56px;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(even)::after {
            left: -56px;
            background: linear-gradient(135deg, #218879, #5dc7b7);
            box-shadow: 0 0 0 6px rgba(72, 169, 154, .12), 0 10px 20px rgba(72, 169, 154, .2);
        }

        .dga-home-body .dga-roadmap-link-card > * {
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
            position: absolute;
            left: 20px;
            top: 22px;
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            font-size: 20px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .82), 0 10px 18px rgba(47, 117, 181, .12);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(2n) .dga-roadmap-step {
            background: linear-gradient(135deg, #e9f8f5, #c9ece7);
            color: #25756b;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(3n) .dga-roadmap-step {
            background: linear-gradient(135deg, #fff7e8, #f5d79c);
            color: #8b5d14;
        }

        .dga-home-body .dga-roadmap-phase,
        .dga-home-body .dga-roadmap-link-card:nth-child(even) .dga-roadmap-phase {
            margin-bottom: 10px;
            padding: 5px 13px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #2f75b5);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(2n) .dga-roadmap-phase {
            background: linear-gradient(90deg, #25756b, #48a99a);
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(3n) .dga-roadmap-phase {
            background: linear-gradient(90deg, #9b6a18, #c08420);
        }

        .dga-home-body .dga-roadmap-link-card h4 {
            margin: 0 0 8px;
            color: #17395d;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.28;
        }

        .dga-home-body .dga-roadmap-link-card p {
            margin: 0;
            color: #52687a;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-roadmap-network {
                padding: 24px 14px 28px;
            }

            .dga-home-body .dga-roadmap-network::before {
                inset: 18px 16px 18px;
                background-image: linear-gradient(180deg, #d4e1eb, #edf6fb);
                width: 44px;
                left: 26px;
                right: auto;
                border-radius: 999px;
            }

            .dga-home-body .dga-roadmap-network::after {
                left: 48px;
            }

            .dga-home-body .dga-roadmap-connection-list {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 52px 0 26px 70px;
            }

            .dga-home-body .dga-roadmap-connection-list::before {
                content: "";
                position: absolute;
                left: 40px;
                top: 58px;
                bottom: 36px;
                width: 4px;
                border-radius: 999px;
                background: repeating-linear-gradient(180deg, #2f75b5 0 14px, transparent 14px 26px);
                z-index: 0;
            }

            .dga-home-body .dga-roadmap-connection-list::after {
                left: 48px;
            }

            .dga-home-body .dga-roadmap-link-card,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd),
            .dga-home-body .dga-roadmap-link-card:nth-child(even),
            .dga-home-body .dga-roadmap-link-card:nth-child(even):hover {
                width: 100%;
                justify-self: stretch;
                transform: none;
            }

            .dga-home-body .dga-roadmap-link-card:hover {
                transform: translateX(4px);
            }

            .dga-home-body .dga-roadmap-link-card::before,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd)::before,
            .dga-home-body .dga-roadmap-link-card:nth-child(even)::before {
                left: -36px;
                right: auto;
                width: 36px;
            }

            .dga-home-body .dga-roadmap-link-card::after,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd)::after,
            .dga-home-body .dga-roadmap-link-card:nth-child(even)::after {
                left: -46px;
                right: auto;
            }
        }

        @media (max-width: 575px) {
            .dga-home-body .dga-roadmap-network {
                padding: 18px 12px 24px;
            }

            .dga-home-body .dga-roadmap-network::before {
                left: 18px;
                width: 36px;
            }

            .dga-home-body .dga-roadmap-network::after,
            .dga-home-body .dga-roadmap-connection-list::after {
                left: 36px;
            }

            .dga-home-body .dga-roadmap-connection-list {
                padding-left: 52px;
                gap: 14px;
            }

            .dga-home-body .dga-roadmap-connection-list::before {
                left: 28px;
            }

            .dga-home-body .dga-roadmap-link-card,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd),
            .dga-home-body .dga-roadmap-link-card:nth-child(even) {
                min-height: 0;
                padding: 16px 14px 16px 64px;
                border-radius: 8px;
            }

            .dga-home-body .dga-roadmap-link-card::before,
            .dga-home-body .dga-roadmap-link-card::after {
                display: block;
            }

            .dga-home-body .dga-roadmap-link-card::before,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd)::before,
            .dga-home-body .dga-roadmap-link-card:nth-child(even)::before {
                left: -24px;
                width: 24px;
            }

            .dga-home-body .dga-roadmap-link-card::after,
            .dga-home-body .dga-roadmap-link-card:nth-child(odd)::after,
            .dga-home-body .dga-roadmap-link-card:nth-child(even)::after {
                left: -34px;
            }

            .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
                left: 14px;
                top: 16px;
                width: 40px;
                height: 40px;
                border-radius: 12px;
                font-size: 16px;
            }
        }

        .dga-home-body .dga-hero-process-grid,
        .dga-home-body .dga-process-flow {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 34px 26px;
            padding: 38px 18px 24px;
            border: 1px solid rgba(188, 211, 225, .9);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(240, 248, 252, .96)),
                linear-gradient(90deg, rgba(47, 117, 181, .045) 1px, transparent 1px),
                linear-gradient(0deg, rgba(72, 169, 154, .04) 1px, transparent 1px);
            background-size: auto, 46px 46px, 46px 46px;
            overflow: hidden;
            counter-reset: camsProcess;
        }

        .dga-home-body .dga-hero-process-grid::before,
        .dga-home-body .dga-process-flow::before {
            content: "";
            position: absolute;
            left: 9%;
            right: 9%;
            top: 112px;
            height: 12px;
            border-radius: 999px;
            background:
                repeating-linear-gradient(90deg, rgba(255, 255, 255, .75) 0 16px, transparent 16px 30px),
                linear-gradient(90deg, #2f75b5, #48a99a 55%, #c08420);
            box-shadow: 0 0 0 8px rgba(215, 231, 241, .6);
            z-index: 0;
        }

        .dga-home-body .dga-hero-process-grid::after,
        .dga-home-body .dga-process-flow::after {
            content: "CAMS Workflow";
            position: absolute;
            left: 18px;
            top: 14px;
            z-index: 2;
            padding: 6px 14px;
            border-radius: 999px;
            background: #174f82;
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 18px rgba(47, 117, 181, .16);
        }

        .dga-home-body .dga-hero-mini-step,
        .dga-home-body .dga-process-step {
            position: relative;
            z-index: 1;
            min-height: 190px;
            padding: 82px 18px 20px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .99), rgba(246, 251, 254, .97));
            box-shadow: 0 18px 36px rgba(32, 54, 74, .11);
            overflow: visible;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2),
        .dga-home-body .dga-process-step:nth-child(2),
        .dga-home-body .dga-hero-mini-step:nth-child(5),
        .dga-home-body .dga-process-step:nth-child(5) {
            transform: translateY(22px);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3),
        .dga-home-body .dga-process-step:nth-child(3),
        .dga-home-body .dga-hero-mini-step:nth-child(6),
        .dga-home-body .dga-process-step:nth-child(6) {
            transform: translateY(44px);
        }

        .dga-home-body .dga-hero-mini-step:hover,
        .dga-home-body .dga-process-step:hover {
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 26px 50px rgba(32, 54, 74, .17);
            transform: translateY(-4px) scale(1.01);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2):hover,
        .dga-home-body .dga-process-step:nth-child(2):hover,
        .dga-home-body .dga-hero-mini-step:nth-child(5):hover,
        .dga-home-body .dga-process-step:nth-child(5):hover {
            transform: translateY(16px) scale(1.01);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3):hover,
        .dga-home-body .dga-process-step:nth-child(3):hover,
        .dga-home-body .dga-hero-mini-step:nth-child(6):hover,
        .dga-home-body .dga-process-step:nth-child(6):hover {
            transform: translateY(38px) scale(1.01);
        }

        .dga-home-body .dga-hero-mini-step::before,
        .dga-home-body .dga-process-step::before {
            counter-increment: camsProcess;
            content: counter(camsProcess);
            position: absolute;
            left: 50%;
            top: -18px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #fff;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 0 0 7px rgba(47, 117, 181, .12), 0 10px 20px rgba(47, 117, 181, .2);
            transform: translateX(-50%);
            z-index: 3;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2n)::before,
        .dga-home-body .dga-process-step:nth-child(2n)::before {
            background: linear-gradient(135deg, #218879, #5dc7b7);
            box-shadow: 0 0 0 7px rgba(72, 169, 154, .12), 0 10px 20px rgba(72, 169, 154, .2);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3n)::before,
        .dga-home-body .dga-process-step:nth-child(3n)::before {
            background: linear-gradient(135deg, #9b6a18, #c08420);
            box-shadow: 0 0 0 7px rgba(192, 132, 32, .13), 0 10px 20px rgba(192, 132, 32, .2);
        }

        .dga-home-body .dga-hero-mini-step::after,
        .dga-home-body .dga-process-step::after {
            content: "\f061";
            position: absolute;
            right: -22px;
            top: 72px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(47, 117, 181, .22);
            border-radius: 50%;
            background: #fff;
            color: #2f75b5;
            font-family: "Font Awesome 5 Free";
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 10px 18px rgba(32, 54, 74, .12);
            z-index: 4;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3n)::after,
        .dga-home-body .dga-process-step:nth-child(3n)::after,
        .dga-home-body .dga-hero-mini-step:last-child::after,
        .dga-home-body .dga-process-step:last-child::after {
            display: none;
        }

        .dga-home-body .dga-hero-mini-step > i,
        .dga-home-body .dga-process-icon {
            position: absolute;
            left: 18px;
            top: 24px;
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            font-size: 20px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 10px 18px rgba(47, 117, 181, .12);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2n) > i,
        .dga-home-body .dga-process-step:nth-child(2n) .dga-process-icon {
            background: linear-gradient(135deg, #e9f8f5, #c9ece7);
            color: #25756b;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3n) > i,
        .dga-home-body .dga-process-step:nth-child(3n) .dga-process-icon {
            background: linear-gradient(135deg, #fff7e8, #f5d79c);
            color: #8b5d14;
        }

        .dga-home-body .dga-hero-process-tag,
        .dga-home-body .dga-process-no {
            position: absolute;
            right: 16px;
            top: 30px;
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #2f75b5);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2n) .dga-hero-process-tag,
        .dga-home-body .dga-process-step:nth-child(2n) .dga-process-no {
            background: linear-gradient(90deg, #25756b, #48a99a);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3n) .dga-hero-process-tag,
        .dga-home-body .dga-process-step:nth-child(3n) .dga-process-no {
            background: linear-gradient(90deg, #9b6a18, #c08420);
        }

        .dga-home-body .dga-hero-mini-step h4,
        .dga-home-body .dga-process-step h4 {
            margin: 0 0 9px;
            color: #17395d;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.28;
        }

        .dga-home-body .dga-hero-mini-step p,
        .dga-home-body .dga-process-step p {
            margin: 0;
            color: #52687a;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-hero-process-grid,
            .dga-home-body .dga-process-flow {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 58px 14px 20px 64px;
            }

            .dga-home-body .dga-hero-process-grid::before,
            .dga-home-body .dga-process-flow::before {
                left: 34px;
                right: auto;
                top: 70px;
                bottom: 36px;
                width: 10px;
                height: auto;
                background:
                    repeating-linear-gradient(180deg, rgba(255, 255, 255, .75) 0 16px, transparent 16px 30px),
                    linear-gradient(180deg, #2f75b5, #48a99a 55%, #c08420);
            }

            .dga-home-body .dga-hero-mini-step,
            .dga-home-body .dga-process-step,
            .dga-home-body .dga-hero-mini-step:nth-child(2),
            .dga-home-body .dga-process-step:nth-child(2),
            .dga-home-body .dga-hero-mini-step:nth-child(3),
            .dga-home-body .dga-process-step:nth-child(3),
            .dga-home-body .dga-hero-mini-step:nth-child(5),
            .dga-home-body .dga-process-step:nth-child(5),
            .dga-home-body .dga-hero-mini-step:nth-child(6),
            .dga-home-body .dga-process-step:nth-child(6) {
                min-height: 0;
                transform: none;
            }

            .dga-home-body .dga-hero-mini-step:hover,
            .dga-home-body .dga-process-step:hover,
            .dga-home-body .dga-hero-mini-step:nth-child(n):hover,
            .dga-home-body .dga-process-step:nth-child(n):hover {
                transform: translateX(4px);
            }

            .dga-home-body .dga-hero-mini-step::before,
            .dga-home-body .dga-process-step::before {
                left: -35px;
                top: 50%;
                transform: translateY(-50%);
            }

            .dga-home-body .dga-hero-mini-step::after,
            .dga-home-body .dga-process-step::after {
                right: auto;
                left: -31px;
                top: auto;
                bottom: -25px;
                transform: rotate(90deg);
            }
        }

        @media (max-width: 575px) {
            .dga-home-body .dga-hero-process-grid,
            .dga-home-body .dga-process-flow {
                padding: 54px 12px 18px 48px;
            }

            .dga-home-body .dga-hero-process-grid::before,
            .dga-home-body .dga-process-flow::before {
                left: 24px;
            }

            .dga-home-body .dga-hero-mini-step,
            .dga-home-body .dga-process-step {
                padding: 74px 14px 16px;
                border-radius: 8px;
            }

            .dga-home-body .dga-hero-mini-step::before,
            .dga-home-body .dga-process-step::before {
                left: -28px;
                width: 32px;
                height: 32px;
                border-width: 4px;
            }

            .dga-home-body .dga-hero-mini-step::after,
            .dga-home-body .dga-process-step::after {
                left: -25px;
            }

            .dga-home-body .dga-hero-mini-step > i,
            .dga-home-body .dga-process-icon {
                left: 14px;
                top: 18px;
                width: 40px;
                height: 40px;
                border-radius: 12px;
                font-size: 16px;
            }

            .dga-home-body .dga-hero-process-tag,
            .dga-home-body .dga-process-no {
                top: 24px;
                right: 12px;
                max-width: calc(100% - 76px);
                white-space: normal;
                text-align: center;
            }
        }

        .dga-home-body .dga-hero-process-grid,
        .dga-home-body .dga-process-flow {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            padding: 58px 18px 48px;
            border: 1px solid rgba(188, 211, 225, .9);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(240, 248, 252, .96)),
                radial-gradient(circle at 50% 0%, rgba(47, 117, 181, .11), transparent 34%);
            counter-reset: camsProcess;
            overflow: hidden;
        }

        .dga-home-body .dga-hero-process-grid::before,
        .dga-home-body .dga-process-flow::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 72px;
            bottom: 52px;
            width: 12px;
            height: auto;
            border-radius: 999px;
            background:
                repeating-linear-gradient(180deg, rgba(255, 255, 255, .82) 0 18px, transparent 18px 32px),
                linear-gradient(180deg, #2f75b5, #48a99a 52%, #c08420);
            box-shadow: 0 0 0 8px rgba(214, 230, 241, .64);
            transform: translateX(-50%);
            z-index: 0;
        }

        .dga-home-body .dga-hero-process-grid::after,
        .dga-home-body .dga-process-flow::after {
            content: "Continuous CAMS Flow";
            left: 50%;
            top: 18px;
            transform: translateX(-50%);
            background: linear-gradient(90deg, #174f82, #2f75b5);
        }

        .dga-home-body .dga-hero-mini-step,
        .dga-home-body .dga-process-step,
        .dga-home-body .dga-hero-mini-step:nth-child(n),
        .dga-home-body .dga-process-step:nth-child(n) {
            width: calc(50% - 58px);
            min-height: 138px;
            margin: -4px 0 22px;
            padding: 22px 22px 20px 88px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: linear-gradient(145deg, #ffffff, #f5fbff);
            box-shadow: 0 16px 34px rgba(32, 54, 74, .11);
            transform: none;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(odd),
        .dga-home-body .dga-process-step:nth-child(odd) {
            justify-self: start;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(even),
        .dga-home-body .dga-process-step:nth-child(even) {
            justify-self: end;
        }

        .dga-home-body .dga-hero-mini-step:hover,
        .dga-home-body .dga-process-step:hover,
        .dga-home-body .dga-hero-mini-step:nth-child(n):hover,
        .dga-home-body .dga-process-step:nth-child(n):hover {
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 24px 48px rgba(32, 54, 74, .16);
            transform: scale(1.012);
        }

        .dga-home-body .dga-hero-mini-step::before,
        .dga-home-body .dga-process-step::before {
            counter-increment: camsProcess;
            content: counter(camsProcess);
            top: 50%;
            width: 42px;
            height: 42px;
            border: 5px solid #fff;
            transform: translateY(-50%);
            z-index: 3;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(odd)::before,
        .dga-home-body .dga-process-step:nth-child(odd)::before {
            left: calc(100% + 37px);
        }

        .dga-home-body .dga-hero-mini-step:nth-child(even)::before,
        .dga-home-body .dga-process-step:nth-child(even)::before {
            left: auto;
            right: calc(100% + 37px);
        }

        .dga-home-body .dga-hero-mini-step::after,
        .dga-home-body .dga-process-step::after {
            content: "";
            display: block;
            top: 50%;
            width: 58px;
            height: 3px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(47, 117, 181, .16), #2f75b5);
            box-shadow: none;
            transform: translateY(-50%);
            z-index: 2;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(odd)::after,
        .dga-home-body .dga-process-step:nth-child(odd)::after {
            display: block;
            left: 100%;
            right: auto;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(even)::after,
        .dga-home-body .dga-process-step:nth-child(even)::after {
            display: block;
            left: auto;
            right: 100%;
            background: linear-gradient(90deg, #48a99a, rgba(72, 169, 154, .16));
        }

        .dga-home-body .dga-hero-mini-step:last-child,
        .dga-home-body .dga-process-step:last-child {
            margin-bottom: 0;
        }

        .dga-home-body .dga-hero-mini-step:last-child::after,
        .dga-home-body .dga-process-step:last-child::after {
            display: block;
        }

        .dga-home-body .dga-hero-mini-step > i,
        .dga-home-body .dga-process-icon {
            left: 20px;
            top: 22px;
            width: 48px;
            height: 48px;
            border-radius: 14px;
        }

        .dga-home-body .dga-hero-process-tag,
        .dga-home-body .dga-process-no {
            position: static;
            margin-bottom: 10px;
            background: rgba(47, 117, 181, .1);
            color: #174f82;
            box-shadow: none;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(2n) .dga-hero-process-tag,
        .dga-home-body .dga-process-step:nth-child(2n) .dga-process-no {
            background: rgba(72, 169, 154, .12);
            color: #25756b;
        }

        .dga-home-body .dga-hero-mini-step:nth-child(3n) .dga-hero-process-tag,
        .dga-home-body .dga-process-step:nth-child(3n) .dga-process-no {
            background: rgba(192, 132, 32, .14);
            color: #8b5d14;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-hero-process-grid,
            .dga-home-body .dga-process-flow {
                padding: 58px 14px 26px 62px;
            }

            .dga-home-body .dga-hero-process-grid::before,
            .dga-home-body .dga-process-flow::before {
                left: 30px;
                top: 78px;
                bottom: 34px;
                transform: none;
            }

            .dga-home-body .dga-hero-process-grid::after,
            .dga-home-body .dga-process-flow::after {
                left: 18px;
                transform: none;
            }

            .dga-home-body .dga-hero-mini-step,
            .dga-home-body .dga-process-step,
            .dga-home-body .dga-hero-mini-step:nth-child(n),
            .dga-home-body .dga-process-step:nth-child(n) {
                width: 100%;
                justify-self: stretch;
                min-height: 0;
                margin: 0 0 16px;
                padding: 20px 18px 18px 82px;
            }

            .dga-home-body .dga-hero-mini-step:nth-child(n)::before,
            .dga-home-body .dga-process-step:nth-child(n)::before {
                left: -52px;
                right: auto;
                top: 50%;
                transform: translateY(-50%);
            }

            .dga-home-body .dga-hero-mini-step:nth-child(n)::after,
            .dga-home-body .dga-process-step:nth-child(n)::after {
                left: -52px;
                right: auto;
                width: 52px;
                transform: translateY(-50%);
            }
        }

        @media (max-width: 575px) {
            .dga-home-body .dga-hero-process-grid,
            .dga-home-body .dga-process-flow {
                padding: 54px 12px 22px 48px;
            }

            .dga-home-body .dga-hero-process-grid::before,
            .dga-home-body .dga-process-flow::before {
                left: 22px;
                width: 9px;
            }

            .dga-home-body .dga-hero-mini-step,
            .dga-home-body .dga-process-step,
            .dga-home-body .dga-hero-mini-step:nth-child(n),
            .dga-home-body .dga-process-step:nth-child(n) {
                padding: 18px 14px 16px 64px;
            }

            .dga-home-body .dga-hero-mini-step:nth-child(n)::before,
            .dga-home-body .dga-process-step:nth-child(n)::before {
                left: -42px;
                width: 34px;
                height: 34px;
                border-width: 4px;
            }

            .dga-home-body .dga-hero-mini-step:nth-child(n)::after,
            .dga-home-body .dga-process-step:nth-child(n)::after {
                left: -42px;
                width: 42px;
            }

            .dga-home-body .dga-hero-mini-step > i,
            .dga-home-body .dga-process-icon {
                left: 14px;
                top: 18px;
                width: 38px;
                height: 38px;
            }
        }

        .dga-home-body #dga-panel-modules,
        .dga-home-body .dga-modules-section {
            background:
                linear-gradient(180deg, #f8fcff 0%, #eef7ff 100%);
        }

        .dga-home-body #dga-panel-modules > .row,
        .dga-home-body .dga-modules-section .row {
            position: relative;
            align-items: stretch;
            padding: 18px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(243, 250, 254, .96)),
                linear-gradient(90deg, rgba(47, 117, 181, .045) 1px, transparent 1px),
                linear-gradient(0deg, rgba(72, 169, 154, .04) 1px, transparent 1px);
            background-size: auto, 44px 44px, 44px 44px;
            box-shadow: 0 18px 42px rgba(32, 54, 74, .11);
            overflow: hidden;
        }

        .dga-home-body #dga-panel-modules > .row::before,
        .dga-home-body .dga-modules-section .row::before {
            content: "User View";
            position: absolute;
            right: 18px;
            top: 16px;
            z-index: 2;
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #2f75b5);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(47, 117, 181, .16);
        }

        .dga-home-body #dga-panel-modules .col-lg-5,
        .dga-home-body .dga-modules-section .col-lg-5 {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            padding: 22px;
            border: 1px solid rgba(47, 117, 181, .16);
            border-radius: 8px;
            background:
                linear-gradient(160deg, rgba(23, 79, 130, .96), rgba(47, 117, 181, .94) 58%, rgba(72, 169, 154, .9)),
                radial-gradient(circle at top right, rgba(240, 184, 79, .24), transparent 34%);
            color: #fff;
            box-shadow: 0 18px 34px rgba(23, 79, 130, .18);
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title span,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title span {
            color: #fff1bf;
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title span::after,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title span::after {
            background: linear-gradient(90deg, #f0b84f, #9be0d5);
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title h3,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title h3 {
            color: #fff;
        }

        .dga-home-body #dga-panel-modules .dga-list,
        .dga-home-body .dga-modules-section .dga-list {
            display: grid;
            gap: 12px;
            counter-reset: reformBenefit;
        }

        .dga-home-body #dga-panel-modules .dga-list li,
        .dga-home-body .dga-modules-section .dga-list li {
            position: relative;
            min-height: 58px;
            margin: 0;
            padding: 14px 14px 14px 56px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .9);
            line-height: 1.45;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        }

        .dga-home-body #dga-panel-modules .dga-list li::before,
        .dga-home-body .dga-modules-section .dga-list li::before {
            counter-increment: reformBenefit;
            content: counter(reformBenefit);
            left: 14px;
            top: 14px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff7d6;
            color: #174f82;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 8px 16px rgba(3, 24, 43, .16);
        }

        .dga-home-body #dga-panel-modules .col-lg-7,
        .dga-home-body .dga-modules-section .col-lg-7 {
            position: relative;
            z-index: 1;
            padding: 22px 20px 22px 28px;
        }

        .dga-home-body .dga-module-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            counter-reset: moduleBoard;
        }

        .dga-home-body .dga-module-grid::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 20px;
            bottom: 20px;
            width: 3px;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(47, 117, 181, .3), rgba(72, 169, 154, .28), rgba(192, 132, 32, .24));
            transform: translateX(-50%);
            z-index: 0;
        }

        .dga-home-body .dga-module {
            position: relative;
            z-index: 1;
            min-height: 178px;
            padding: 22px 18px 18px 88px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: linear-gradient(145deg, #ffffff, #f6fbff);
            box-shadow: 0 14px 30px rgba(32, 54, 74, .1);
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .dga-home-body .dga-module:hover {
            transform: translateY(-4px) scale(1.01);
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 24px 46px rgba(32, 54, 74, .16);
        }

        .dga-home-body .dga-module::before {
            counter-increment: moduleBoard;
            content: counter(moduleBoard);
            position: absolute;
            right: 14px;
            top: 14px;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(47, 117, 181, .1);
            color: #174f82;
            font-size: 11px;
            font-weight: 900;
            box-shadow: none;
        }

        .dga-home-body .dga-module:nth-child(2n)::before {
            background: rgba(72, 169, 154, .12);
            color: #25756b;
        }

        .dga-home-body .dga-module:nth-child(3n)::before {
            background: rgba(192, 132, 32, .14);
            color: #8b5d14;
        }

        .dga-home-body .dga-module::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, #2f75b5, #62a9df);
        }

        .dga-home-body .dga-module:nth-child(2n)::after {
            background: linear-gradient(180deg, #218879, #5dc7b7);
        }

        .dga-home-body .dga-module:nth-child(3n)::after {
            background: linear-gradient(180deg, #9b6a18, #f0b84f);
        }

        .dga-home-body .dga-module-identity {
            position: absolute;
            left: 18px;
            top: 22px;
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            font-size: 22px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 12px 22px rgba(47, 117, 181, .14);
        }

        .dga-home-body .dga-module:nth-child(2n) .dga-module-identity {
            background: linear-gradient(135deg, #e9f8f5, #c9ece7);
            color: #25756b;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 12px 22px rgba(72, 169, 154, .14);
        }

        .dga-home-body .dga-module:nth-child(3n) .dga-module-identity {
            background: linear-gradient(135deg, #fff7e8, #f5d79c);
            color: #8b5d14;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 12px 22px rgba(192, 132, 32, .14);
        }

        .dga-home-body .dga-module-label {
            display: inline-flex;
            width: max-content;
            margin: 0 34px 8px 0;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(47, 117, 181, .1);
            color: #174f82;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dga-home-body .dga-module:nth-child(2n) .dga-module-label {
            background: rgba(72, 169, 154, .12);
            color: #25756b;
        }

        .dga-home-body .dga-module:nth-child(3n) .dga-module-label {
            background: rgba(192, 132, 32, .14);
            color: #8b5d14;
        }

        .dga-home-body .dga-module h4 {
            margin: 0 0 12px;
            color: #17395d;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.25;
        }

        .dga-home-body .dga-module ul {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dga-home-body .dga-module li {
            position: relative;
            margin: 0;
            padding-left: 20px;
            color: #52687a;
            font-size: 13px;
            line-height: 1.45;
        }

        .dga-home-body .dga-module li::before {
            content: "";
            position: absolute;
            left: 0;
            top: .58em;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2f75b5;
            box-shadow: 0 0 0 4px rgba(47, 117, 181, .11);
        }

        .dga-home-body .dga-module:nth-child(2n) li::before {
            background: #48a99a;
            box-shadow: 0 0 0 4px rgba(72, 169, 154, .12);
        }

        .dga-home-body .dga-module:nth-child(3n) li::before {
            background: #c08420;
            box-shadow: 0 0 0 4px rgba(192, 132, 32, .12);
        }

        @media (max-width: 991px) {
            .dga-home-body #dga-panel-modules > .row,
            .dga-home-body .dga-modules-section .row {
                padding: 14px;
            }

            .dga-home-body #dga-panel-modules .col-lg-7,
            .dga-home-body .dga-modules-section .col-lg-7 {
                padding: 20px 0 0;
            }

            .dga-home-body .dga-module-grid {
                grid-template-columns: 1fr;
            }

            .dga-home-body .dga-module-grid::before {
                left: 21px;
                top: 18px;
                bottom: 18px;
            }

            .dga-home-body .dga-module {
                padding-left: 72px;
            }
        }

        @media (max-width: 575px) {
            .dga-home-body #dga-panel-modules > .row::before,
            .dga-home-body .dga-modules-section .row::before {
                position: static;
                display: inline-flex;
                width: max-content;
                margin-bottom: 12px;
            }

            .dga-home-body #dga-panel-modules .col-lg-5,
            .dga-home-body .dga-modules-section .col-lg-5 {
                padding: 16px;
            }

            .dga-home-body #dga-panel-modules .dga-list li,
            .dga-home-body .dga-modules-section .dga-list li {
                padding: 12px 12px 12px 50px;
            }

            .dga-home-body .dga-module {
                min-height: 0;
                padding: 18px 14px 16px 64px;
            }

            .dga-home-body .dga-module::before {
                right: 12px;
                top: 12px;
                width: 26px;
                height: 26px;
            }

            .dga-home-body .dga-module-identity {
                left: 14px;
                top: 18px;
                width: 38px;
                height: 38px;
                border-radius: 12px;
                font-size: 16px;
            }
        }

        .dga-home-body #dga-panel-modules,
        .dga-home-body .dga-modules-section {
            background: linear-gradient(180deg, #f7fbff, #edf7ff);
        }

        .dga-home-body #dga-panel-modules > .row,
        .dga-home-body .dga-modules-section .row {
            padding: 20px;
            border: 1px solid rgba(196, 216, 229, .95);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(244, 250, 254, .96));
            box-shadow: 0 16px 38px rgba(32, 54, 74, .1);
        }

        .dga-home-body #dga-panel-modules > .row::before,
        .dga-home-body .dga-modules-section .row::before {
            content: "CAMS Services";
            background: linear-gradient(90deg, #174f82, #48a99a);
        }

        .dga-home-body #dga-panel-modules .col-lg-5,
        .dga-home-body .dga-modules-section .col-lg-5 {
            background:
                linear-gradient(180deg, #ffffff, #f2f9ff);
            color: #20364a;
            border-color: rgba(196, 216, 229, .95);
            box-shadow: 0 12px 28px rgba(32, 54, 74, .08);
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title span,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title span {
            color: #2f75b5;
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title h3,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title h3 {
            color: #17395d;
        }

        .dga-home-body #dga-panel-modules .dga-list li,
        .dga-home-body .dga-modules-section .dga-list li {
            background: #fff;
            border-color: rgba(196, 216, 229, .95);
            color: #52687a;
            box-shadow: 0 8px 18px rgba(32, 54, 74, .06);
        }

        .dga-home-body #dga-panel-modules .dga-list li::before,
        .dga-home-body .dga-modules-section .dga-list li::before {
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
        }

        .dga-home-body .dga-module-grid {
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .dga-home-body .dga-module-grid::before {
            content: none;
        }

        .dga-home-body .dga-module {
            min-height: 214px;
            padding: 92px 18px 18px;
            border: 1px solid rgba(190, 213, 226, .95);
            background:
                linear-gradient(180deg, rgba(234, 246, 255, .78) 0 74px, #ffffff 74px),
                linear-gradient(145deg, #ffffff, #f6fbff);
            box-shadow: 0 14px 30px rgba(32, 54, 74, .1);
            text-align: left;
        }

        .dga-home-body .dga-module:nth-child(2n) {
            background:
                linear-gradient(180deg, rgba(233, 248, 245, .9) 0 74px, #ffffff 74px),
                linear-gradient(145deg, #ffffff, #f5fcfb);
        }

        .dga-home-body .dga-module:nth-child(3n) {
            background:
                linear-gradient(180deg, rgba(255, 247, 232, .95) 0 74px, #ffffff 74px),
                linear-gradient(145deg, #ffffff, #fffbf4);
        }

        .dga-home-body .dga-module:nth-child(4n) {
            background:
                linear-gradient(180deg, rgba(239, 242, 255, .95) 0 74px, #ffffff 74px),
                linear-gradient(145deg, #ffffff, #f7f8ff);
        }

        .dga-home-body .dga-module:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 46px rgba(32, 54, 74, .16);
        }

        .dga-home-body .dga-module::after {
            left: 0;
            right: 0;
            top: 0;
            bottom: auto;
            width: auto;
            height: 5px;
        }

        .dga-home-body .dga-module::before {
            right: 16px;
            top: 18px;
            width: 30px;
            height: 30px;
            background: #fff;
            color: #174f82;
            box-shadow: 0 8px 16px rgba(47, 117, 181, .12);
        }

        .dga-home-body .dga-module-identity {
            left: 18px;
            top: 18px;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            font-size: 23px;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            box-shadow: 0 14px 24px rgba(47, 117, 181, .22);
        }

        .dga-home-body .dga-module:nth-child(2n) .dga-module-identity {
            background: linear-gradient(135deg, #218879, #5dc7b7);
            color: #fff;
        }

        .dga-home-body .dga-module:nth-child(3n) .dga-module-identity {
            background: linear-gradient(135deg, #9b6a18, #f0b84f);
            color: #fff;
        }

        .dga-home-body .dga-module:nth-child(4n) .dga-module-identity {
            background: linear-gradient(135deg, #565db8, #9096ea);
            color: #fff;
        }

        .dga-home-body .dga-module-label {
            margin: 0 0 8px;
            background: rgba(47, 117, 181, .1);
        }

        .dga-home-body .dga-module h4 {
            padding-right: 0;
            font-size: 17px;
        }

        .dga-home-body .dga-module ul {
            gap: 7px;
        }

        @media (max-width: 575px) {
            .dga-home-body #dga-panel-modules > .row,
            .dga-home-body .dga-modules-section .row {
                padding: 14px;
            }

            .dga-home-body .dga-module {
                min-height: 0;
                padding: 82px 14px 16px;
            }

            .dga-home-body .dga-module-identity {
                left: 14px;
                top: 16px;
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .dga-home-body .dga-module::before {
                right: 14px;
                top: 18px;
            }
        }

        .dga-home-body #dga-panel-modules > .row,
        .dga-home-body .dga-modules-section .row {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(239, 248, 255, .96));
        }

        .dga-home-body #dga-panel-modules > .row::before,
        .dga-home-body .dga-modules-section .row::before {
            content: "CAMS Module Flow";
            background: linear-gradient(90deg, #174f82, #2f75b5);
        }

        .dga-home-body .dga-module-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            padding: 54px 12px 28px;
            counter-reset: moduleBoard;
        }

        .dga-home-body .dga-module-grid::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 28px;
            bottom: 28px;
            width: 10px;
            border-radius: 999px;
            background:
                repeating-linear-gradient(180deg, rgba(255, 255, 255, .75) 0 16px, transparent 16px 30px),
                linear-gradient(180deg, #2f75b5, #48a99a 56%, #c08420);
            box-shadow: 0 0 0 8px rgba(214, 230, 241, .62);
            transform: translateX(-50%);
            z-index: 0;
        }

        .dga-home-body .dga-module {
            position: relative;
            z-index: 1;
            width: calc(50% - 54px);
            min-height: 150px;
            margin: 0 0 20px;
            padding: 20px 18px 18px 86px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: linear-gradient(145deg, #ffffff, #f6fbff);
            text-align: left;
            box-shadow: 0 15px 32px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-module:nth-child(odd) {
            justify-self: start;
        }

        .dga-home-body .dga-module:nth-child(even) {
            justify-self: end;
            background: linear-gradient(145deg, #ffffff, #f5fcfb);
        }

        .dga-home-body .dga-module:nth-child(3n) {
            background: linear-gradient(145deg, #ffffff, #fffbf4);
        }

        .dga-home-body .dga-module:hover {
            transform: scale(1.012);
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 24px 48px rgba(32, 54, 74, .16);
        }

        .dga-home-body .dga-module::before {
            counter-increment: moduleBoard;
            content: counter(moduleBoard);
            position: absolute;
            top: 50%;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #fff;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 0 0 7px rgba(47, 117, 181, .12), 0 10px 20px rgba(47, 117, 181, .2);
            transform: translateY(-50%);
            z-index: 3;
        }

        .dga-home-body .dga-module:nth-child(odd)::before {
            left: calc(100% + 35px);
            right: auto;
        }

        .dga-home-body .dga-module:nth-child(even)::before {
            left: auto;
            right: calc(100% + 35px);
            background: linear-gradient(135deg, #218879, #5dc7b7);
            box-shadow: 0 0 0 7px rgba(72, 169, 154, .12), 0 10px 20px rgba(72, 169, 154, .2);
        }

        .dga-home-body .dga-module:nth-child(3n)::before {
            background: linear-gradient(135deg, #9b6a18, #f0b84f);
            box-shadow: 0 0 0 7px rgba(192, 132, 32, .13), 0 10px 20px rgba(192, 132, 32, .2);
        }

        .dga-home-body .dga-module::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 54px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(47, 117, 181, .16), #2f75b5);
            transform: translateY(-50%);
            z-index: 2;
        }

        .dga-home-body .dga-module:nth-child(odd)::after {
            left: 100%;
            right: auto;
        }

        .dga-home-body .dga-module:nth-child(even)::after {
            left: auto;
            right: 100%;
            background: linear-gradient(90deg, #48a99a, rgba(72, 169, 154, .16));
        }

        .dga-home-body .dga-module-identity {
            left: 18px;
            top: 20px;
            width: 50px;
            height: 50px;
            border-radius: 16px;
            font-size: 21px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 10px 18px rgba(47, 117, 181, .12);
        }

        .dga-home-body .dga-module:nth-child(2n) .dga-module-identity {
            background: linear-gradient(135deg, #e9f8f5, #c9ece7);
            color: #25756b;
        }

        .dga-home-body .dga-module:nth-child(3n) .dga-module-identity {
            background: linear-gradient(135deg, #fff7e8, #f5d79c);
            color: #8b5d14;
        }

        .dga-home-body .dga-module-label {
            margin: 0 0 8px;
        }

        .dga-home-body .dga-module h4 {
            margin-bottom: 10px;
            font-size: 17px;
        }

        .dga-home-body .dga-module:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-module-grid {
                padding: 54px 0 20px 58px;
            }

            .dga-home-body .dga-module-grid::before {
                left: 24px;
                transform: none;
            }

            .dga-home-body .dga-module,
            .dga-home-body .dga-module:nth-child(odd),
            .dga-home-body .dga-module:nth-child(even) {
                width: 100%;
                justify-self: stretch;
                min-height: 0;
                padding: 18px 16px 16px 78px;
            }

            .dga-home-body .dga-module:nth-child(n)::before {
                left: -53px;
                right: auto;
            }

            .dga-home-body .dga-module:nth-child(n)::after {
                left: -53px;
                right: auto;
                width: 53px;
            }
        }

        @media (max-width: 575px) {
            .dga-home-body .dga-module-grid {
                padding: 48px 0 16px 44px;
            }

            .dga-home-body .dga-module-grid::before {
                left: 18px;
                width: 8px;
            }

            .dga-home-body .dga-module,
            .dga-home-body .dga-module:nth-child(odd),
            .dga-home-body .dga-module:nth-child(even) {
                padding: 74px 14px 16px;
            }

            .dga-home-body .dga-module:nth-child(n)::before {
                left: -40px;
                width: 32px;
                height: 32px;
                border-width: 4px;
            }

            .dga-home-body .dga-module:nth-child(n)::after {
                left: -40px;
                width: 40px;
            }

            .dga-home-body .dga-module-identity {
                left: 14px;
                top: 16px;
                width: 42px;
                height: 42px;
                font-size: 17px;
            }
        }

        .dga-home-body #dga-panel-modules > .row,
        .dga-home-body .dga-modules-section .row {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .99), rgba(242, 249, 253, .97)),
                linear-gradient(90deg, rgba(47, 117, 181, .04) 1px, transparent 1px),
                linear-gradient(0deg, rgba(72, 169, 154, .035) 1px, transparent 1px);
            background-size: auto, 46px 46px, 46px 46px;
        }

        .dga-home-body #dga-panel-modules > .row::before,
        .dga-home-body .dga-modules-section .row::before {
            content: "Module Matrix";
            background: linear-gradient(90deg, #174f82, #48a99a);
        }

        .dga-home-body .dga-module-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            padding: 0;
            counter-reset: moduleBoard;
        }

        .dga-home-body .dga-module-grid::before {
            content: none;
        }

        .dga-home-body .dga-module,
        .dga-home-body .dga-module:nth-child(odd),
        .dga-home-body .dga-module:nth-child(even) {
            width: 100%;
            min-height: 188px;
            margin: 0;
            justify-self: stretch;
            padding: 20px 18px 18px 92px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #ffffff, #f7fbff);
            box-shadow: 0 14px 30px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-module:nth-child(2n) {
            background: linear-gradient(180deg, #ffffff, #f5fcfb);
        }

        .dga-home-body .dga-module:nth-child(3n) {
            background: linear-gradient(180deg, #ffffff, #fffbf4);
        }

        .dga-home-body .dga-module:hover,
        .dga-home-body .dga-module:nth-child(n):hover {
            transform: translateY(-4px);
            border-color: rgba(47, 117, 181, .42);
            box-shadow: 0 24px 46px rgba(32, 54, 74, .16);
        }

        .dga-home-body .dga-module::before,
        .dga-home-body .dga-module:nth-child(odd)::before,
        .dga-home-body .dga-module:nth-child(even)::before {
            counter-increment: moduleBoard;
            content: "M" counter(moduleBoard);
            left: auto;
            right: 14px;
            top: 14px;
            width: auto;
            height: 26px;
            min-width: 38px;
            padding: 0 10px;
            border: 0;
            border-radius: 999px;
            background: rgba(47, 117, 181, .1);
            color: #174f82;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .06em;
            box-shadow: none;
            transform: none;
        }

        .dga-home-body .dga-module:nth-child(2n)::before {
            background: rgba(72, 169, 154, .12);
            color: #25756b;
        }

        .dga-home-body .dga-module:nth-child(3n)::before {
            background: rgba(192, 132, 32, .14);
            color: #8b5d14;
        }

        .dga-home-body .dga-module::after,
        .dga-home-body .dga-module:nth-child(odd)::after,
        .dga-home-body .dga-module:nth-child(even)::after {
            content: "";
            left: 0;
            right: auto;
            top: 0;
            bottom: 0;
            width: 6px;
            height: auto;
            border-radius: 0;
            background: linear-gradient(180deg, #2f75b5, #62a9df);
            transform: none;
        }

        .dga-home-body .dga-module:nth-child(2n)::after {
            background: linear-gradient(180deg, #218879, #5dc7b7);
        }

        .dga-home-body .dga-module:nth-child(3n)::after {
            background: linear-gradient(180deg, #9b6a18, #f0b84f);
        }

        .dga-home-body .dga-module-identity {
            left: 20px;
            top: 22px;
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 10px 18px rgba(47, 117, 181, .12);
        }

        .dga-home-body .dga-module-label {
            margin-right: 46px;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-module-grid {
                grid-template-columns: 1fr;
                padding: 0;
            }

            .dga-home-body .dga-module,
            .dga-home-body .dga-module:nth-child(odd),
            .dga-home-body .dga-module:nth-child(even) {
                width: 100%;
                min-height: 0;
                padding: 20px 18px 18px 88px;
            }
        }

        @media (max-width: 575px) {
            .dga-home-body .dga-module,
            .dga-home-body .dga-module:nth-child(odd),
            .dga-home-body .dga-module:nth-child(even) {
                padding: 76px 14px 16px;
            }

            .dga-home-body .dga-module-identity {
                left: 14px;
                top: 16px;
                width: 44px;
                height: 44px;
            }
        }

        .dga-home-body #dga-panel-modules,
        .dga-home-body .dga-modules-section {
            background:
                linear-gradient(180deg, #e6f3fb 0%, #d8edf4 100%) !important;
        }

        .dga-home-body #dga-panel-modules > .row,
        .dga-home-body .dga-modules-section .row {
            border-color: rgba(88, 139, 172, .42);
            background:
                linear-gradient(145deg, rgba(20, 69, 108, .95), rgba(38, 112, 145, .93) 52%, rgba(57, 143, 135, .9)),
                radial-gradient(circle at top right, rgba(240, 184, 79, .22), transparent 34%);
            box-shadow: 0 24px 52px rgba(16, 58, 88, .22);
        }

        .dga-home-body #dga-panel-modules > .row::before,
        .dga-home-body .dga-modules-section .row::before {
            content: "Module Deck";
            background: rgba(255, 255, 255, .16);
            color: #fff;
            box-shadow: none;
        }

        .dga-home-body #dga-panel-modules .col-lg-5,
        .dga-home-body .dga-modules-section .col-lg-5 {
            border-color: rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .11);
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title span,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title span {
            color: #fff1bf;
        }

        .dga-home-body #dga-panel-modules .col-lg-5 .dga-section-title h3,
        .dga-home-body .dga-modules-section .col-lg-5 .dga-section-title h3 {
            color: #fff;
        }

        .dga-home-body #dga-panel-modules .dga-list li,
        .dga-home-body .dga-modules-section .dga-list li {
            border-color: rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .13);
            color: rgba(255, 255, 255, .88);
            box-shadow: none;
        }

        .dga-home-body #dga-panel-modules .dga-list li::before,
        .dga-home-body .dga-modules-section .dga-list li::before {
            background: #fff7d6;
            color: #174f82;
        }

        .dga-home-body #dga-panel-modules .col-lg-7 .dga-section-title span,
        .dga-home-body .dga-modules-section .col-lg-7 .dga-section-title span {
            color: #fff1bf;
        }

        .dga-home-body #dga-panel-modules .col-lg-7 .dga-section-title h3,
        .dga-home-body .dga-modules-section .col-lg-7 .dga-section-title h3 {
            color: #fff;
        }

        .dga-home-body .dga-module,
        .dga-home-body .dga-module:nth-child(odd),
        .dga-home-body .dga-module:nth-child(even),
        .dga-home-body .dga-module:nth-child(3n) {
            border-color: rgba(255, 255, 255, .26);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(232, 246, 255, .86));
            box-shadow: 0 18px 36px rgba(3, 24, 43, .16);
        }

        .dga-home-body .dga-module:nth-child(2n) {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(226, 248, 244, .86));
        }

        .dga-home-body .dga-module:nth-child(3n) {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(255, 242, 214, .86));
        }

        .dga-home-body .dga-module:hover,
        .dga-home-body .dga-module:nth-child(n):hover {
            box-shadow: 0 28px 52px rgba(3, 24, 43, .24);
        }

        .dga-home-body #dga-panel-modules .col-lg-5,
        .dga-home-body .dga-modules-section .col-lg-5,
        .dga-home-body #dga-panel-modules .col-lg-7,
        .dga-home-body .dga-modules-section .col-lg-7 {
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        }

        .dga-home-body #dga-panel-modules .col-lg-7,
        .dga-home-body .dga-modules-section .col-lg-7 {
            padding: 22px;
        }

        .dga-home-body #dga-panel-modules .dga-section-title,
        .dga-home-body .dga-modules-section .dga-section-title {
            margin-bottom: 18px;
            padding: 14px 16px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
        }

        .dga-home-body #dga-panel-modules .dga-section-title span,
        .dga-home-body .dga-modules-section .dga-section-title span {
            color: #fff1bf;
        }

        .dga-home-body #dga-panel-modules .dga-section-title h3,
        .dga-home-body .dga-modules-section .dga-section-title h3 {
            color: #fff;
            font-size: clamp(22px, 2vw, 30px);
        }

        .dga-home-body #dga-panel-modules .dga-list li,
        .dga-home-body .dga-modules-section .dga-list li,
        .dga-home-body .dga-module,
        .dga-home-body .dga-module:nth-child(odd),
        .dga-home-body .dga-module:nth-child(even),
        .dga-home-body .dga-module:nth-child(3n) {
            border: 1px solid rgba(255, 255, 255, .36);
            background: rgba(255, 255, 255, .92);
            color: #52687a;
            box-shadow: 0 16px 32px rgba(3, 24, 43, .14);
        }

        .dga-home-body #dga-panel-modules .dga-list li,
        .dga-home-body .dga-modules-section .dga-list li {
            min-height: 64px;
            display: flex;
            align-items: center;
            padding: 14px 14px 14px 58px;
        }

        .dga-home-body #dga-panel-modules .dga-list li::before,
        .dga-home-body .dga-modules-section .dga-list li::before {
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #2f75b5, #62a9df);
            color: #fff;
        }

        .dga-home-body .dga-module h4 {
            color: #17395d;
        }

        .dga-home-body .dga-module li {
            color: #52687a;
        }

        @media (max-width: 991px) {
            .dga-home-body #dga-panel-modules .col-lg-7,
            .dga-home-body .dga-modules-section .col-lg-7 {
                margin-top: 16px;
            }
        }

        .dga-home-body #dga-panel-support,
        .dga-home-body .dga-support-section {
            background: linear-gradient(180deg, #f7fbff 0%, #eef8ff 100%);
        }

        .dga-home-body #dga-panel-support .dga-panel,
        .dga-home-body .dga-support-section .dga-panel {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .99), rgba(244, 250, 254, .97));
            box-shadow: 0 18px 42px rgba(32, 54, 74, .1);
        }

        .dga-home-body #dga-panel-support .dga-panel::before,
        .dga-home-body .dga-support-section .dga-panel::before {
            content: "Support Desk";
            position: absolute;
            right: 18px;
            top: 16px;
            z-index: 2;
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #2f75b5);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(47, 117, 181, .16);
        }

        .dga-home-body #dga-panel-support .row,
        .dga-home-body .dga-support-section .row {
            align-items: stretch !important;
        }

        .dga-home-body #dga-panel-support .col-lg-5,
        .dga-home-body .dga-support-section .col-lg-5 {
            position: relative;
            display: flex;
            align-items: center;
            padding: 22px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff, #eef8ff);
            box-shadow: 0 12px 28px rgba(32, 54, 74, .08);
        }

        .dga-home-body #dga-panel-support .col-lg-5::before,
        .dga-home-body .dga-support-section .col-lg-5::before {
            content: "\f590";
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            border-radius: 18px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            font-family: "Font Awesome 5 Free";
            font-size: 22px;
            font-weight: 900;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 10px 18px rgba(47, 117, 181, .12);
        }

        .dga-home-body #dga-panel-support .dga-section-title,
        .dga-home-body .dga-support-section .dga-section-title {
            margin-bottom: 0;
        }

        .dga-home-body #dga-panel-support .dga-section-title span,
        .dga-home-body .dga-support-section .dga-section-title span {
            color: #2f75b5;
        }

        .dga-home-body #dga-panel-support .dga-section-title h3,
        .dga-home-body .dga-support-section .dga-section-title h3 {
            color: #17395d;
            font-size: clamp(22px, 2vw, 30px);
        }

        .dga-home-body #dga-panel-support .col-lg-7,
        .dga-home-body .dga-support-section .col-lg-7 {
            padding-left: 20px;
        }

        .dga-home-body #dga-panel-support .dga-list,
        .dga-home-body .dga-support-section .dga-list {
            display: grid;
            gap: 12px;
            counter-reset: supportItem;
        }

        .dga-home-body #dga-panel-support .dga-list li,
        .dga-home-body .dga-support-section .dga-list li {
            position: relative;
            min-height: 62px;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 14px 16px 14px 62px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: #fff;
            color: #52687a;
            line-height: 1.45;
            box-shadow: 0 12px 26px rgba(32, 54, 74, .08);
        }

        .dga-home-body #dga-panel-support .dga-list li::before,
        .dga-home-body .dga-support-section .dga-list li::before {
            counter-increment: supportItem;
            content: counter(supportItem);
            left: 16px;
            top: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            font-size: 12px;
            font-weight: 900;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84), 0 8px 16px rgba(47, 117, 181, .12);
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-support .dga-list li:nth-child(2n)::before,
        .dga-home-body .dga-support-section .dga-list li:nth-child(2n)::before {
            background: linear-gradient(135deg, #e9f8f5, #c9ece7);
            color: #25756b;
        }

        @media (max-width: 991px) {
            .dga-home-body #dga-panel-support .col-lg-7,
            .dga-home-body .dga-support-section .col-lg-7 {
                padding-left: 0;
                padding-top: 16px;
            }
        }

        @media (max-width: 575px) {
            .dga-home-body #dga-panel-support .dga-panel::before,
            .dga-home-body .dga-support-section .dga-panel::before {
                position: static;
                display: inline-flex;
                margin-bottom: 12px;
            }

            .dga-home-body #dga-panel-support .col-lg-5,
            .dga-home-body .dga-support-section .col-lg-5 {
                align-items: flex-start;
                padding: 16px;
            }

            .dga-home-body #dga-panel-support .dga-list li,
            .dga-home-body .dga-support-section .dga-list li {
                padding: 12px 12px 12px 54px;
            }
        }

        .dga-home-body .dga-bottom-updates {
            position: relative;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #ffffff, #eef8ff);
            box-shadow: 0 18px 42px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-bottom-updates::before {
            /* content: "Auto Scroll"; */
            position: absolute;
            left: 22px;
            top: 18px;
            z-index: 2;
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #2f75b5);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(47, 117, 181, .16);
        }

        .dga-home-body .dga-bottom-updates-center {
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(32, 54, 74, .09);
        }

        .dga-home-body .dga-bottom-updates-head {
            padding: 16px 18px;
            background: linear-gradient(90deg, #174f82, #2f75b5 58%, #48a99a);
        }

        .dga-home-body .dga-bottom-updates-head h3 {
            font-size: 24px;
        }

        .dga-home-body .dga-bottom-updates-window {
            position: relative;
            height: clamp(300px, 38vh, 420px);
            overflow: hidden;
            padding: 14px;
            background: linear-gradient(180deg, #f8fcff, #eef8ff);
        }

        .dga-home-body .dga-bottom-updates-window::before,
        .dga-home-body .dga-bottom-updates-window::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 38px;
            z-index: 2;
            pointer-events: none;
        }

        .dga-home-body .dga-bottom-updates-window::before {
            top: 0;
            background: linear-gradient(180deg, #f8fcff, rgba(248, 252, 255, 0));
        }

        .dga-home-body .dga-bottom-updates-window::after {
            bottom: 0;
            background: linear-gradient(0deg, #eef8ff, rgba(238, 248, 255, 0));
        }

        .dga-home-body .dga-bottom-updates-window .dga-updates-track {
            display: grid;
            gap: 12px;
            max-height: none;
            overflow: visible;
            padding: 0;
            animation: dgaUpdatesScroll 28s linear infinite;
        }

        .dga-home-body .dga-bottom-updates-window:hover .dga-updates-track,
        .dga-home-body .dga-bottom-updates-window:focus-within .dga-updates-track {
            animation-play-state: paused;
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-item {
            min-height: 0;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-card {
            align-items: center;
            min-height: 72px;
            border: 1px solid rgba(190, 213, 226, .95);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 10px 22px rgba(32, 54, 74, .08);
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-date-badge {
            border-radius: 8px;
            background: linear-gradient(135deg, #eaf6ff, #c9e6f8);
            color: #174f82;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .84);
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-card p {
            color: #20364a;
            font-size: 13px;
            line-height: 1.55;
        }

        .dga-home-body .dga-update-photo-frame {
            border-radius: 8px;
            border-style: solid;
            background:
                linear-gradient(180deg, #ffffff, #f2f9ff);
        }

        .dga-home-body .dga-update-frame-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dga-home-body .dga-update-photo-frame.has-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(13, 45, 73, .04), rgba(13, 45, 73, .34));
            pointer-events: none;
        }

        .dga-home-body .dga-update-photo-frame.has-image .dga-update-frame-label {
            position: absolute;
            left: 14px;
            bottom: 14px;
            z-index: 2;
            background: rgba(255, 255, 255, .88);
            color: #174f82;
            box-shadow: 0 10px 20px rgba(13, 45, 73, .16);
        }

        .dga-home-body .dga-overview-panel {
            padding: clamp(16px, 1.8vw, 24px);
        }

        .dga-home-body .dga-overview-panel::after {
            top: clamp(62px, 7vw, 86px);
        }

        .dga-home-body .dga-overview-panel > h3 {
            min-height: 64px;
            margin-bottom: clamp(14px, 1.4vw, 20px);
            padding: 14px 22px 14px 76px;
            font-size: clamp(22px, 1.8vw, 28px);
        }

        .dga-home-body .dga-overview-panel > h3::before {
            left: 20px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
        }

        .dga-home-body .dga-overview-panel > h3::after {
            top: 12px;
            right: 16px;
            padding: 5px 10px;
            font-size: 10px;
        }

        .dga-home-body .dga-overview-panel .dga-hero-card-grid {
            gap: clamp(12px, 1.5vw, 18px);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card {
            padding: 22px 20px 20px 76px;
            box-shadow: 0 14px 34px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card::before {
            left: 22px;
            top: 22px;
            width: 38px;
            height: 38px;
            border-radius: 12px;
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card h4 {
            margin-bottom: 12px;
            font-size: clamp(18px, 1.45vw, 22px);
        }

        .dga-home-body .dga-overview-panel .dga-hero-list {
            gap: 8px;
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li {
            min-height: 48px;
            padding: 9px 12px 9px 46px;
            line-height: 1.45;
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li::before {
            left: 12px;
            width: 24px;
            height: 24px;
            font-size: 11px;
        }

        .dga-home-body .dga-header {
            position: fixed;
            left: 0;
            right: 0;
            top: var(--dga-topbar-height, 36px);
            z-index: 70;
            background: rgba(255, 255, 255, .98);
            border-bottom: 1px solid #c8e0ef;
            box-shadow: 0 12px 30px rgba(32, 54, 74, .1);
        }

        .dga-home-body .dga-topbar {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            z-index: 80;
            background: linear-gradient(90deg, var(--dga-theme-dark) 0%, var(--dga-theme-primary) 58%, var(--dga-theme-secondary) 100%);
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .16);
        }

        .dga-home-body {
            padding-top: var(--dga-fixed-top-height, 164px);
        }

        .dga-home-body .dga-topbar .container {
            width: 100%;
            max-width: none;
            margin-right: 0;
            margin-left: 0;
            padding-right: 12px !important;
            padding-left: 12px !important;
        }

        .dga-home-body .dga-topbar .dga-brand-accessibility {
            justify-content: flex-start;
            margin-right: auto;
        }

        .dga-home-body .dga-topbar .dga-topbar-tools {
            margin-left: auto;
        }

        .dga-home-body .dga-department-strip {
            position: relative;
            overflow: visible;
            border-top: 1px solid rgba(255, 255, 255, .22);
            background:
                linear-gradient(90deg, var(--dga-theme-dark) 0%, var(--dga-theme-primary) 46%, var(--dga-theme-secondary) 100%);
            box-shadow: 0 10px 24px var(--dga-theme-shadow);
        }

        .dga-home-body .dga-department-strip::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255, 244, 199, .14), transparent 24% 72%, rgba(155, 224, 213, .16));
            pointer-events: none;
        }

        .dga-home-body .dga-department-strip::after {
            height: 3px;
            background: linear-gradient(90deg, var(--dga-theme-accent) 0 26%, #ffffff 26% 54%, var(--dga-theme-light) 54% 100%);
        }

        .dga-home-body .dga-department-grid {
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 8px;
            padding: 11px 0 13px;
            overflow-x: visible;
            overflow-y: visible;
        }

        .dga-home-body .dga-department-grid::-webkit-scrollbar {
            display: none;
        }

        .dga-home-body .dga-department-grid::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(255, 244, 199, .7);
        }

        .dga-home-body .dga-department-item::after {
            content: "";
            position: absolute;
            left: -6px;
            right: -6px;
            top: 100%;
            height: 20px;
        }

        .dga-home-body .dga-department-item {
            --dept-pill-a: #174f82;
            --dept-pill-b: #2f75b5;
            --dept-pill-soft: rgba(255, 255, 255, .12);
            --dept-pill-ring: rgba(255, 255, 255, .2);
            --dept-pill-text: #ffffff;
        }

        .dga-home-body .dga-department-item.is-dept-lfa {
            --dept-pill-a: #f8fcff;
            --dept-pill-b: #dff1fb;
            --dept-pill-soft: rgba(255, 255, 255, .92);
            --dept-pill-ring: rgba(255, 244, 199, .86);
            --dept-pill-text: #174f82;
        }

        .dga-home-body .dga-department-item.is-dept-hria {
            --dept-pill-a: #fff8dc;
            --dept-pill-b: #dff7f3;
            --dept-pill-soft: rgba(255, 255, 255, .9);
            --dept-pill-ring: rgba(155, 224, 213, .78);
            --dept-pill-text: #1c625d;
        }

        .dga-home-body .dga-department-item.is-dept-sgad {
            --dept-pill-a: #eef8ff;
            --dept-pill-b: #d9ebff;
            --dept-pill-ring: rgba(201, 230, 248, .9);
            --dept-pill-text: #174f82;
        }

        .dga-home-body .dga-department-item.is-dept-dca {
            --dept-pill-a: #f0fbf5;
            --dept-pill-b: #d8f0e5;
            --dept-pill-ring: rgba(157, 215, 181, .86);
            --dept-pill-text: #246447;
        }

        .dga-home-body .dga-department-item.is-dept-milk {
            --dept-pill-a: #fff7eb;
            --dept-pill-b: #f6e2c2;
            --dept-pill-ring: rgba(240, 184, 79, .72);
            --dept-pill-text: #805513;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
            min-height: 42px;
            min-width: 76px;
            padding: 9px 18px;
            border: 1px solid var(--dept-pill-ring);
            border-radius: 8px;
            background:
                linear-gradient(135deg, var(--dept-pill-a), var(--dept-pill-b));
            color: var(--dept-pill-text);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .45), 0 9px 18px rgba(13, 45, 73, .14);
            backdrop-filter: blur(8px);
            transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .dga-home-body .dga-department-title::before {
            left: 14px;
            right: 14px;
            bottom: 5px;
            height: 2px;
            background: linear-gradient(90deg, #fff4c7, #9be0d5);
        }

        .dga-home-body .dga-department-title strong {
            color: inherit;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .dga-home-body .dga-department-arrow {
            display: none;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background:
                linear-gradient(135deg, #ffffff, var(--dept-pill-a));
            border-color: #fff4c7;
            color: var(--dept-pill-text);
            box-shadow: 0 14px 28px rgba(13, 45, 73, .2);
            transform: translateY(-1px);
        }

        .dga-home-body .dga-department-item:hover .dga-department-arrow,
        .dga-home-body .dga-department-item:focus-within .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: #fff4c7;
            color: #174f82;
            transform: rotate(180deg);
        }

        .dga-home-body .dga-department-menu {
            left: 0;
            top: calc(100% + 10px);
            min-width: 300px;
            padding: 12px;
            border: 1px solid rgba(47, 117, 181, .18);
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(247, 251, 255, .92) 58%, rgba(240, 248, 246, .9));
            box-shadow: 0 24px 48px rgba(13, 45, 73, .2), inset 0 1px 0 rgba(255, 255, 255, .84);
            backdrop-filter: blur(16px);
            transform: translateY(14px) scale(.96);
            transform-origin: top left;
            opacity: 0;
            pointer-events: none;
            display: block;
            counter-reset: deptOption;
            transition: opacity .42s ease, transform .42s cubic-bezier(.18, .84, .28, 1);
            z-index: 80;
        }

        .dga-home-body .dga-department-menu::before {
            display: none;
        }

        .dga-home-body .dga-department-item:hover .dga-department-menu,
        .dga-home-body .dga-department-item:focus-within .dga-department-menu,
        .dga-home-body .dga-department-item.is-open .dga-department-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .dga-home-body .dga-department-menu a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            margin: 0 0 8px;
            padding: 10px 14px 10px 48px;
            border: 1px solid rgba(47, 117, 181, .2);
            border-radius: 8px;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .76), rgba(255, 255, 255, .48));
            color: #174f82;
            font-size: 13px;
            font-weight: 800;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .72);
            backdrop-filter: blur(12px);
            opacity: 0;
            transform: translateY(14px) scale(.98);
            transition: opacity .34s ease, transform .34s cubic-bezier(.18, .84, .28, 1), border-color .18s ease, background .18s ease, color .18s ease;
        }

        .dga-home-body .dga-department-menu a:nth-child(1) {
            transition-delay: .1s;
            background: linear-gradient(90deg, rgba(226, 244, 255, .82), rgba(255, 255, 255, .58));
            border-color: rgba(107, 166, 205, .22);
        }

        .dga-home-body .dga-department-menu a:nth-child(2) {
            transition-delay: .17s;
            background: linear-gradient(90deg, rgba(226, 247, 241, .82), rgba(255, 255, 255, .58));
            border-color: rgba(112, 181, 162, .22);
        }

        .dga-home-body .dga-department-menu a:nth-child(3) {
            transition-delay: .24s;
            background: linear-gradient(90deg, rgba(255, 245, 219, .86), rgba(255, 255, 255, .58));
            border-color: rgba(221, 178, 98, .26);
        }

        .dga-home-body .dga-department-menu a:nth-child(4) {
            transition-delay: .31s;
            background: linear-gradient(90deg, rgba(233, 239, 255, .82), rgba(255, 255, 255, .58));
            border-color: rgba(133, 151, 210, .22);
        }

        .dga-home-body .dga-department-menu a:nth-child(5) {
            transition-delay: .38s;
            background: linear-gradient(90deg, rgba(245, 236, 255, .82), rgba(255, 255, 255, .58));
            border-color: rgba(172, 138, 213, .22);
        }

        .dga-home-body .dga-department-menu a:nth-child(6) {
            transition-delay: .45s;
            background: linear-gradient(90deg, rgba(232, 248, 230, .82), rgba(255, 255, 255, .58));
            border-color: rgba(132, 183, 128, .22);
        }

        .dga-home-body .dga-department-menu a:nth-child(7) {
            transition-delay: .52s;
            background: linear-gradient(90deg, rgba(255, 235, 242, .84), rgba(255, 255, 255, .58));
            border-color: rgba(211, 132, 158, .24);
        }

        .dga-home-body .dga-department-item:hover .dga-department-menu a,
        .dga-home-body .dga-department-item:focus-within .dga-department-menu a,
        .dga-home-body .dga-department-item.is-open .dga-department-menu a {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .dga-home-body .dga-department-menu a::before {
            content: counter(deptOption);
            counter-increment: deptOption;
            position: absolute;
            left: 13px;
            top: 50%;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff4c7;
            color: #174f82;
            font-size: 11px;
            font-weight: 900;
            box-shadow: 0 8px 14px rgba(13, 45, 73, .18);
            transform: translateY(-50%);
        }

        .dga-home-body .dga-department-menu a:last-child {
            margin-bottom: 0;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            border-color: rgba(221, 178, 98, .64);
            background:
                linear-gradient(90deg, rgba(255, 244, 199, .96), rgba(255, 255, 255, .86));
            color: #123f68;
            transform: translateX(4px);
            box-shadow: 0 12px 22px rgba(13, 45, 73, .16), inset 0 1px 0 rgba(255, 255, 255, .76);
            transition-delay: 0s;
        }

        .dga-home-body .dga-department-menu a:hover::before,
        .dga-home-body .dga-department-menu a:focus::before {
            background: #fff4c7;
            color: #174f82;
            box-shadow: 0 8px 16px rgba(13, 45, 73, .2);
        }

        .dga-home-body .dga-brand h1,
        .dga-home-body .dga-nav-link,
        .dga-home-body .dga-section-title span,
        .dga-home-body .dga-panel h4,
        .dga-home-body .dga-hero-menu-title,
        .dga-home-body .dga-flow-box-copy strong,
        .dga-home-body #dga-panel-wings .dga-wing-link-card h4 {
            color: var(--dga-theme-primary);
        }

        .dga-home-body .dga-screen-reader-link,
        .dga-home-body .dga-screen-reader-link:hover,
        .dga-home-body .dga-screen-reader-link:focus,
        .dga-home-body .dga-font-controls button,
        .dga-home-body .dga-language-switch button:hover,
        .dga-home-body .dga-language-switch button:focus,
        .dga-home-body .dga-theme-switch button:hover,
        .dga-home-body .dga-theme-switch button:focus,
        .dga-home-body .dga-language-switch button.is-active,
        .dga-home-body .dga-theme-switch button.is-active {
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-font-controls button:hover,
        .dga-home-body .dga-font-controls button:focus,
        .dga-home-body .dga-font-controls button.is-active,
        .dga-home-body .dga-nav-link.primary,
        .dga-home-body .dga-nav-link.primary:hover,
        .dga-home-body .dga-nav-link.primary:focus,
        .dga-home-body .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link {
            background: var(--dga-theme-primary);
            border-color: var(--dga-theme-primary);
            color: #ffffff;
        }

        .dga-home-body .dga-page-sections-band {
            background:
                linear-gradient(115deg, var(--dga-theme-soft) 0%, var(--dga-theme-soft-2) 42%, #ffffff 42%, var(--dga-bg) 100%);
        }

        .dga-home-body .dga-hero-menu-card {
            background: linear-gradient(180deg, #ffffff, var(--dga-theme-soft));
        }

        .dga-home-body .dga-hero-menu-card::before,
        .dga-home-body .dga-home-footer-strip,
        .dga-home-body .dga-bottom-updates-head,
        .dga-home-body .dga-update-frame::before,
        .dga-home-body .dga-footer-credit::before {
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-hero-menu-title::after,
        .dga-home-body .dga-flow-org-caption span::after,
        .dga-home-body .dga-section-title span::after,
        .dga-home-body #dga-panel-modules .dga-section-title span::after {
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-menu-kicker,
        .dga-home-body .dga-menu-subtitle,
        .dga-home-body .dga-home-intro .dga-eyebrow,
        .dga-home-body .dga-home-panel .dga-eyebrow,
        .dga-home-body .dga-update-frame-label {
            border-color: var(--dga-line);
            background: var(--dga-soft-gold);
            color: color-mix(in srgb, var(--dga-theme-dark) 76%, #5b3c10);
        }

        .dga-home-body .dga-menu-kicker::before,
        .dga-home-body .dga-department-menu a::before,
        .dga-home-body .dga-footer-top-link i,
        .dga-home-body .dga-footer-support-list li::before,
        .dga-home-body #dga-panel-support .dga-list li::before,
        .dga-home-body #dga-panel-modules .dga-list li::before {
            background: var(--dga-theme-accent);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-hero-menu-button,
        .dga-home-body .dga-hero-menu-button:nth-child(n) {
            --dga-menu-dark: var(--dga-theme-primary);
            --dga-menu-light: var(--dga-theme-light);
            --dga-menu-accent: var(--dga-theme-primary);
            --dga-menu-active-gradient: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-light));
            --dga-menu-hover-gradient: linear-gradient(135deg, #ffffff, var(--dga-theme-hover));
            --dga-menu-text: var(--dga-theme-dark);
            --dga-menu-border: var(--dga-line);
            --dga-menu-icon-bg: color-mix(in srgb, var(--dga-theme-primary) 13%, transparent);
            --dga-menu-shadow: var(--dga-theme-shadow);
        }

        .dga-home-body .dga-hero-menu-button:hover,
        .dga-home-body .dga-hero-menu-button:focus,
        .dga-home-body .dga-hero-menu-button.is-active {
            border-color: var(--dga-theme-primary);
            box-shadow: 0 18px 32px var(--dga-theme-shadow);
        }

        .dga-home-body .dga-hero-menu-button:hover i,
        .dga-home-body .dga-hero-menu-button:focus i {
            background: color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-department-menu {
            border-color: var(--dga-line);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .96), color-mix(in srgb, var(--dga-theme-soft) 72%, #ffffff) 58%, color-mix(in srgb, var(--dga-theme-soft-2) 70%, #ffffff));
        }

        .dga-home-body .dga-department-menu a {
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-department-menu a:nth-child(n) {
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-soft) 76%, #ffffff), rgba(255, 255, 255, .62));
            border-color: color-mix(in srgb, var(--dga-theme-primary) 22%, #ffffff);
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            border-color: color-mix(in srgb, var(--dga-theme-accent) 64%, #ffffff);
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-accent) 34%, #ffffff), rgba(255, 255, 255, .9));
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-footer {
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-dark) 88%, #082f4f), var(--dga-theme-primary));
        }

	        .dga-home-body .dga-home-footer-strip {
	            position: relative;
	            grid-template-columns: minmax(360px, .92fr) minmax(460px, 1.08fr);
	            gap: 14px;
	            padding: 20px 84px 20px 20px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-light) 22%, rgba(255, 255, 255, .18));
            border-radius: 8px;
            background:
                linear-gradient(120deg, color-mix(in srgb, var(--dga-theme-dark) 92%, #071e31) 0%, var(--dga-theme-primary) 48%, color-mix(in srgb, var(--dga-theme-secondary) 84%, #102f4a) 100%);
            box-shadow: 0 20px 44px color-mix(in srgb, var(--dga-theme-shadow) 70%, rgba(7, 30, 49, .22));
            overflow: hidden;
        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 5px;
            background:
                linear-gradient(90deg, var(--dga-theme-accent) 0 22%, #ffffff 22% 42%, var(--dga-theme-light) 42% 72%, var(--dga-theme-accent) 72% 100%);
        }

        .dga-home-body .dga-home-footer-strip::after {
            content: "";
            position: absolute;
            inset: 5px 0 auto;
            height: 1px;
            background: rgba(255, 255, 255, .18);
            pointer-events: none;
        }

        .dga-home-body .dga-footer-credit {
            min-height: 136px;
            padding: 20px 18px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-left: 5px solid var(--dga-theme-accent);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .15), rgba(255, 255, 255, .07));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .18);
        }

        .dga-home-body .dga-footer-credit span {
            color: rgba(255, 255, 255, .78);
            font-size: 11px;
            font-weight: 900;
            line-height: 1.5;
            letter-spacing: .08em;
        }

        .dga-home-body .dga-footer-credit strong {
            color: var(--dga-theme-accent);
            font-size: 28px;
            letter-spacing: 0;
        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 136px;
            padding: 20px 22px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-left: 5px solid var(--dga-theme-light);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .97), color-mix(in srgb, var(--dga-theme-soft) 64%, #ffffff));
            box-shadow: 0 14px 30px rgba(13, 45, 73, .14);
        }

        .dga-home-body .dga-home-footer-meta {
            border-left-color: var(--dga-theme-accent);
        }

        .dga-home-body .dga-home-footer-heading {
            color: var(--dga-theme-dark);
            letter-spacing: .02em;
        }

        .dga-home-body .dga-home-footer-heading i {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--dga-theme-primary) 13%, #ffffff);
            color: var(--dga-theme-primary);
        }

        .dga-home-body .dga-home-footer-mail,
        .dga-home-body .dga-footer-support-list li {
            border-color: color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            background: rgba(255, 255, 255, .86);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-home-footer-mail:hover,
        .dga-home-body .dga-home-footer-mail:focus {
            border-color: color-mix(in srgb, var(--dga-theme-accent) 52%, #ffffff);
            background: linear-gradient(90deg, var(--dga-soft-gold), #ffffff);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-footer-top-link,
        .dga-home-body .dga-footer-support-list li {
            border-color: color-mix(in srgb, var(--dga-theme-light) 28%, rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-footer-top-link {
            right: 22px;
            top: 50%;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(255, 255, 255, .58);
            border-radius: 8px;
            background:
                linear-gradient(135deg, var(--dga-theme-accent), color-mix(in srgb, var(--dga-theme-accent) 76%, #ffffff));
            color: var(--dga-theme-dark);
            box-shadow: 0 16px 28px rgba(13, 45, 73, .24);
            transform: translateY(-50%);
        }

        .dga-home-body .dga-footer-top-link i {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .58);
            color: var(--dga-theme-dark);
            font-size: 15px;
            transition: transform .2s ease, background .2s ease;
        }

        .dga-home-body .dga-footer-top-link:hover,
        .dga-home-body .dga-footer-top-link:focus {
            border-color: rgba(255, 255, 255, .88);
            background:
                linear-gradient(135deg, #ffffff, var(--dga-theme-accent));
            color: var(--dga-theme-dark);
            transform: translateY(-50%) scale(1.04);
        }

        .dga-home-body .dga-footer-top-link:hover i,
        .dga-home-body .dga-footer-top-link:focus i {
            background: #ffffff;
            transform: translateY(-2px);
        }

        .dga-home-body .dga-page-sections-band {
            padding-bottom: 8px;
        }

	        .dga-home-body .dga-home-footer-strip {
	            grid-template-columns: minmax(360px, .92fr) minmax(460px, 1.08fr);
	            gap: 10px;
	            margin: 10px 0 0;
            padding: 12px;
            border-radius: 8px 8px 0 0;
            background:
                linear-gradient(115deg, color-mix(in srgb, var(--dga-theme-dark) 94%, #061a2a) 0%, var(--dga-theme-primary) 50%, color-mix(in srgb, var(--dga-theme-secondary) 82%, #082f4f) 100%);
            box-shadow: 0 -4px 22px rgba(13, 45, 73, .12);
        }

        .dga-home-body .dga-home-footer-strip::before {
            height: 4px;
            background: linear-gradient(90deg, var(--dga-theme-accent), var(--dga-theme-light), var(--dga-theme-accent));
        }

        .dga-home-body .dga-footer-credit,
        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 84px;
            padding: 14px 16px;
            border-radius: 8px;
        }

        .dga-home-body .dga-footer-credit {
            display: flex;
            align-items: center;
            justify-content: center;
            border-left-width: 4px;
        }

        .dga-home-body .dga-footer-credit span,
        .dga-home-body .dga-footer-credit span strong {
            display: inline;
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.35;
            letter-spacing: .01em;
            text-transform: none;
            color: #ffffff;
            white-space: nowrap;
        }

	        .dga-home-body .dga-footer-credit span strong {
	            margin-left: 8px;
	            color: var(--dga-theme-accent);
	            font-size: 18px;
	        }

	        .dga-home-body .dga-home-footer-credit-row {
	            margin: 0;
	            padding: 10px 18px;
	            border-top: 1px solid rgba(255, 255, 255, .18);
	            border-radius: 0 0 8px 8px;
	            background:
	                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-dark) 94%, #061a2a), color-mix(in srgb, var(--dga-theme-secondary) 82%, #082f4f));
	            color: #ffffff;
	            font-size: 14px;
	            font-weight: 900;
	            line-height: 1.35;
	            text-align: center;
	            box-shadow: 0 10px 22px rgba(13, 45, 73, .12);
	        }

	        .dga-home-body .dga-home-footer-credit-row strong {
	            color: var(--dga-theme-accent);
	        }

        .dga-home-body .dga-home-footer-center,
        .dga-home-body .dga-home-footer-meta {
            min-height: 84px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .97), color-mix(in srgb, var(--dga-theme-soft) 54%, #ffffff));
        }

        .dga-home-body .dga-home-footer-heading {
            margin-bottom: 8px;
            font-size: 11px;
        }

        .dga-home-body .dga-home-footer-heading i,
        .dga-home-body .dga-home-footer-mail i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: color-mix(in srgb, var(--dga-theme-primary) 13%, #ffffff);
            color: var(--dga-theme-primary);
            font-size: 13px;
            line-height: 1;
        }

        .dga-home-body .dga-home-footer-heading i::before,
        .dga-home-body .dga-home-footer-mail i::before,
        .dga-home-body .dga-footer-top-link i::before {
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-weight: 900;
        }

        .dga-home-body .dga-home-footer-mail {
            width: max-content;
            max-width: 100%;
            min-height: 36px;
            margin-top: 0;
            padding: 4px 12px 4px 4px;
            border-radius: 8px;
            cursor: default;
            text-decoration: none;
        }

        .dga-home-body .dga-home-footer-note {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.45;
        }

        .dga-home-body .dga-footer-support-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px 8px;
        }

        .dga-home-body .dga-footer-support-list li {
            min-height: 32px;
            padding: 7px 9px 7px 34px;
            font-size: 12px;
            line-height: 1.25;
        }

        .dga-home-body .dga-footer-support-list li::before {
            left: 10px;
            width: 14px;
            height: 14px;
        }

        .dga-home-body .dga-footer-top-link {
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

        .dga-home-body .dga-footer-top-link i {
            width: auto;
            height: auto;
            border-radius: 0;
            background: transparent;
            color: var(--dga-theme-primary);
            font-size: 17px;
        }

        .dga-home-body .dga-footer-top-link:hover,
        .dga-home-body .dga-footer-top-link:focus {
            background: linear-gradient(135deg, #ffffff, var(--dga-theme-accent));
            transform: translateY(-3px);
        }

        .dga-home-body .dga-footer-top-link:hover i,
        .dga-home-body .dga-footer-top-link:focus i {
            background: transparent;
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-department-arrow {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            color: inherit;
            font-size: 11px;
            line-height: 1;
            transition: transform .2s ease, background .2s ease, color .2s ease;
        }

        .dga-home-body .dga-department-title:hover .dga-department-arrow,
        .dga-home-body .dga-department-title:focus .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: color-mix(in srgb, var(--dga-theme-accent) 38%, #ffffff);
            color: var(--dga-theme-dark);
            transform: rotate(180deg);
        }

		        .dga-home-body .dga-bottom-updates {
		            width: min(100%, 1080px);
		            grid-template-columns: minmax(165px, .72fr) minmax(260px, .86fr) minmax(165px, .72fr);
		            gap: 8px;
		            align-items: stretch;
		            margin: 6px auto 0;
		            padding: 8px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-soft) 74%, #ffffff) 58%, #ffffff 100%);
            box-shadow: 0 16px 34px rgba(32, 54, 74, .09);
        }

        .dga-home-body .dga-bottom-updates::before {
            content: none;
        }

		        .dga-home-body .dga-update-photo-frame {
		            min-height: clamp(92px, 12vh, 132px);
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-primary) 10%, #ffffff), #ffffff);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .82), 0 12px 26px rgba(32, 54, 74, .08);
        }

        .dga-home-body .dga-update-photo-frame.has-image::after {
            background:
                linear-gradient(180deg, rgba(9, 42, 67, .04), rgba(9, 42, 67, .38)),
                linear-gradient(90deg, rgba(255, 244, 199, .18), transparent 44%, rgba(155, 224, 213, .2));
        }

        .dga-home-body .dga-update-photo-frame.is-minister-photo .dga-update-frame-image {
            object-position: center top;
        }

	        .dga-home-body .dga-bottom-updates-center {
	            min-height: 0;
	            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 24%, #ffffff);
	            border-radius: 8px;
	            background:
	                linear-gradient(180deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-soft) 46%, #ffffff) 100%);
	            box-shadow: 0 10px 22px rgba(32, 54, 74, .07);
	            overflow: hidden;
	        }

		        .dga-home-body .dga-bottom-updates-head {
		            min-height: 38px;
		            padding: 6px 10px 6px 12px;
		            border-bottom: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
	            background:
	                linear-gradient(90deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-soft) 80%, #ffffff) 100%);
	            color: var(--dga-theme-dark);
	        }

		        .dga-home-body .dga-bottom-updates-head span {
		            padding: 3px 8px;
	            border: 1px solid color-mix(in srgb, var(--dga-theme-accent) 50%, #ffffff);
	            background: color-mix(in srgb, var(--dga-theme-accent) 22%, #ffffff);
	            color: var(--dga-theme-dark);
	            font-size: 10px;
	            letter-spacing: .04em;
	        }

		        .dga-home-body .dga-bottom-updates-head h3 {
		            font-size: 16px;
		            color: var(--dga-theme-primary);
		            letter-spacing: 0;
		            text-transform: uppercase;
		        }

		        .dga-home-body .dga-bottom-updates-window {
		            height: clamp(78px, 10vh, 108px);
		            padding: 6px 7px;
	            background:
	                linear-gradient(180deg, #f8fcff, #ffffff);
	        }

		        .dga-home-body .dga-bottom-updates-window::before,
		        .dga-home-body .dga-bottom-updates-window::after {
		            height: 12px;
	        }

		        .dga-home-body .dga-bottom-updates-window .dga-updates-track {
		            gap: 5px;
	            animation-duration: 22s;
	        }

		        .dga-home-body .dga-bottom-updates-window .dga-update-card {
		            min-height: 34px;
		            gap: 6px;
		            padding: 5px 8px;
		            position: relative;
		            overflow: hidden;
	            border-radius: 8px;
	            border-color: color-mix(in srgb, var(--dga-theme-primary) 14%, #ffffff);
	            background:
	                linear-gradient(90deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-soft) 36%, #ffffff) 100%);
	            box-shadow: 0 6px 14px rgba(32, 54, 74, .055);
	        }

	        .dga-home-body .dga-bottom-updates-window .dga-update-card::before {
	            content: "";
	            position: absolute;
	            left: 0;
	            top: 0;
	            bottom: 0;
	            width: 4px;
	            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary));
	        }

		        .dga-home-body .dga-bottom-updates-window .dga-update-date-badge {
		            min-width: 58px;
		            min-height: 20px;
	            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 20%, #ffffff);
	            border-radius: 999px;
	            background: color-mix(in srgb, var(--dga-theme-primary) 9%, #ffffff);
	            color: var(--dga-theme-primary);
	            font-size: 10px;
	            box-shadow: none;
	        }

		        .dga-home-body .dga-bottom-updates-window .dga-update-card p {
		            color: var(--dga-ink);
		            font-size: 10px;
		            line-height: 1.25;
		        }

        .dga-home-body .dga-overview-panel .dga-overview-card-icon,
        .dga-home-body .dga-wing-icon,
        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step {
            color: var(--dga-theme-primary);
        }

        .dga-home-body .dga-overview-panel .dga-overview-card-icon::before,
        .dga-home-body .dga-wing-icon::before,
        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step::before {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-weight: 900;
            line-height: 1;
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(1) .dga-overview-card-icon::before {
            content: "\f19c";
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2) .dga-overview-card-icon::before {
            content: "\f46c";
        }

        .dga-home-body .dga-wing-link-card:nth-child(1) .dga-wing-icon::before {
            content: "\f058";
        }

        .dga-home-body .dga-wing-link-card:nth-child(2) .dga-wing-icon::before {
            content: "\f021";
        }

        .dga-home-body .dga-wing-link-card:nth-child(3) .dga-wing-icon::before {
            content: "\f201";
        }

        .dga-home-body .dga-wing-link-card:nth-child(4) .dga-wing-icon::before {
            content: "\f109";
        }

        .dga-home-body .dga-wing-link-card:nth-child(5) .dga-wing-icon::before {
            content: "\f24e";
        }

	        .dga-home-body .dga-wing-icon > i,
	        .dga-home-body .dga-roadmap-link-card .dga-roadmap-step > i {
	            display: none;
	        }

        /* Redesigned Functional Map panel */
        .dga-home-body #dga-panel-wings {
            background:
                linear-gradient(135deg, #ffffff 0%, #eef8fb 52%, #ffffff 100%);
        }

        .dga-home-body #dga-panel-wings > h3 {
            margin-bottom: 16px;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network {
            position: relative;
            padding: clamp(20px, 2.4vw, 30px);
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(90deg, rgba(47, 117, 181, .08) 1px, transparent 1px),
                linear-gradient(180deg, rgba(72, 169, 154, .08) 1px, transparent 1px),
                linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(239, 248, 255, .96));
            background-size: 42px 42px, 42px 42px, auto;
            box-shadow: 0 18px 42px rgba(32, 54, 74, .10);
            overflow: hidden;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::before,
        .dga-home-body #dga-panel-wings .dga-wing-network::after {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub {
            position: relative;
            width: min(100%, 360px);
            min-height: 68px;
            margin: 0 auto 28px;
            border: 1px solid rgba(255, 255, 255, .46);
            border-radius: 8px;
            background:
                linear-gradient(135deg, var(--dga-theme-dark), var(--dga-theme-primary) 58%, var(--dga-theme-secondary));
            box-shadow: 0 18px 36px var(--dga-theme-shadow);
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub::after {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub span {
            color: rgba(255, 255, 255, .84);
            font-size: 11px;
            letter-spacing: .12em;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub strong {
            color: #ffffff;
            font-size: clamp(24px, 2.2vw, 32px);
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            gap: 18px;
            padding-top: 0;
            counter-reset: wingMap;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card {
            position: relative;
            min-height: 210px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 58px 22px 20px;
            grid-column: auto;
            overflow: visible;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff);
            border-left: 6px solid var(--wing-accent, var(--dga-theme-primary));
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff, color-mix(in srgb, var(--wing-accent, var(--dga-theme-primary)) 8%, #ffffff));
            text-align: left;
            box-shadow: 0 14px 30px rgba(32, 54, 74, .08);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(1) {
            --wing-accent: var(--dga-theme-primary);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(2) {
            --wing-accent: var(--dga-theme-secondary);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(3) {
            --wing-accent: #c08420;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(4) {
            --wing-accent: #565db8;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
            --wing-accent: #b85d42;
            grid-column: auto;
            width: 100%;
            justify-self: stretch;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::before {
            content: "";
            position: absolute;
            left: 100%;
            top: 50%;
            z-index: 4;
            width: 18px;
            height: 14px;
            border-radius: 0;
            background: linear-gradient(90deg, var(--wing-accent, var(--dga-theme-primary)), var(--dga-theme-secondary));
            clip-path: polygon(0 42%, calc(100% - 7px) 42%, calc(100% - 7px) 0, 100% 50%, calc(100% - 7px) 100%, calc(100% - 7px) 58%, 0 58%);
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:not(:last-child) .dga-wing-icon::after {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:last-child::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::after {
            counter-increment: wingMap;
            content: counter(wingMap, decimal-leading-zero);
            position: absolute;
            left: 18px;
            top: 16px;
            width: auto;
            min-width: 44px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            border: 1px solid color-mix(in srgb, var(--wing-accent, var(--dga-theme-primary)) 24%, #ffffff);
            border-radius: 999px;
            background: color-mix(in srgb, var(--wing-accent, var(--dga-theme-primary)) 12%, #ffffff);
            color: var(--wing-accent, var(--dga-theme-primary));
            font-size: 11px;
            font-weight: 900;
            box-shadow: none;
            transform: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:hover {
            transform: translateY(-4px);
            border-color: color-mix(in srgb, var(--wing-accent, var(--dga-theme-primary)) 32%, #ffffff);
            box-shadow: 0 22px 46px rgba(32, 54, 74, .15);
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon {
            position: absolute;
            left: auto;
            right: 18px;
            top: 14px;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: color-mix(in srgb, var(--wing-accent, var(--dga-theme-primary)) 13%, #ffffff);
            color: var(--wing-accent, var(--dga-theme-primary));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9), 0 10px 20px rgba(32, 54, 74, .08);
            transform: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card h4 {
            margin: 0 0 10px;
            color: #17395d;
            font-size: clamp(17px, 1.35vw, 21px);
            font-weight: 900;
            line-height: 1.25;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card p {
            margin: 0;
            color: #4d6478;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.55;
        }

        @media (max-width: 991px) {
            .dga-home-body #dga-panel-wings .dga-wing-map-hub {
                margin-bottom: 26px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-map-hub::after,
            .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before,
            .dga-home-body #dga-panel-wings .dga-wing-link-card::before,
            .dga-home-body #dga-panel-wings .dga-wing-link-card .dga-wing-icon::after {
                display: none;
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
                grid-template-columns: 1fr;
                gap: 14px;
                padding-top: 0;
            }

            .dga-home-body #dga-panel-wings .dga-wing-link-card,
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
                grid-column: auto;
            }
        }

	        .dga-home-body .dga-roadmap-link-card:nth-child(1) .dga-roadmap-step::before {
	            content: "\f19c";
	        }

        .dga-home-body .dga-roadmap-link-card:nth-child(2) .dga-roadmap-step::before {
            content: "\f058";
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(3) .dga-roadmap-step::before {
            content: "\f108";
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(4) .dga-roadmap-step::before {
            content: "\f0e8";
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(5) .dga-roadmap-step::before {
            content: "\f2b5";
        }

        .dga-home-body .dga-roadmap-link-card:nth-child(6) .dga-roadmap-step::before {
            content: "\f201";
        }

        .dga-home-body .dga-home-panel {
            background:
                linear-gradient(180deg, #ffffff 0%, var(--dga-theme-soft) 46%, var(--dga-bg) 100%);
        }

        .dga-home-body .dga-home-panel::before {
            display: block;
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-primary) 8%, transparent), transparent 56%),
                linear-gradient(180deg, rgba(255, 255, 255, .82), transparent 44%);
        }

        .dga-home-body .dga-home-panel::after {
            display: none;
        }

        .dga-home-body .dga-home-panel .dga-home-intro {
            position: relative;
            min-height: clamp(320px, 28vh, 300px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: clamp(18px, 2.4vw, 30px) clamp(18px, 2.8vw, 38px) 0;
            padding: clamp(34px, 4vw, 58px) clamp(28px, 3.8vw, 56px) clamp(34px, 4vw, 58px) clamp(50px, 5.4vw, 82px);
            border: 1px solid var(--dga-line);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-soft) 72%, #ffffff) 58%, #ffffff 100%);
            color: var(--dga-ink);
            box-shadow: 0 18px 42px var(--dga-shadow), inset 0 1px 0 rgba(255, 255, 255, .86);
        }

        .dga-home-body .dga-home-panel .dga-home-intro::before {
            left: 0;
            right: auto;
            top: 0;
            bottom: 0;
            width: 10px;
            height: auto;
            border-radius: 8px 0 0 8px;
            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
        }

        .dga-home-body .dga-home-panel .dga-home-intro::after {
            content: "";
            position: absolute;
            left: clamp(22px, 2.5vw, 34px);
            top: clamp(28px, 3.2vw, 42px);
            bottom: clamp(28px, 3.2vw, 42px);
            width: 2px;
            border-radius: 999px;
            background:
                linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            box-shadow:
                0 0 0 5px color-mix(in srgb, var(--dga-theme-primary) 10%, transparent),
                0 46px 0 0 var(--dga-theme-accent);
            opacity: .9;
        }

        .dga-home-body .dga-home-panel .dga-home-intro h2 {
            max-width: 980px;
            margin-top: 0;
            margin-bottom: 14px;
            color: var(--dga-theme-dark);
            text-shadow: none;
        }

        .dga-home-body .dga-home-panel .dga-home-intro p {
            max-width: 920px;
            color: var(--dga-muted);
        }

        .dga-home-body .dga-home-panel .dga-stat-grid {
            position: relative;
            padding: clamp(22px, 2.8vw, 36px) clamp(18px, 2.8vw, 38px);
            background: transparent;
        }

        .dga-home-body .dga-home-panel .dga-stat-grid::before {
            content: "";
            position: absolute;
            left: clamp(34px, 4vw, 58px);
            right: clamp(34px, 4vw, 58px);
            top: 50%;
            height: 3px;
            border-radius: 999px;
            background:
                linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
            opacity: .28;
            pointer-events: none;
        }

        .dga-home-body .dga-home-panel .dga-stat {
            z-index: 1;
            min-height: 128px;
            justify-content: flex-start;
            align-items: flex-start;
            padding: 24px 22px 20px;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 20%, #ffffff);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-soft) 62%, #ffffff));
        }

        .dga-home-body .dga-home-panel .dga-home-stat-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            border-radius: 13px;
            background:
                linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-size: 19px;
            box-shadow: 0 14px 28px var(--dga-theme-shadow);
            position: relative;
            z-index: 1;
        }

        .dga-home-body .dga-home-panel .dga-home-stat-icon::before {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            line-height: 1;
        }

        .dga-home-body .dga-home-panel .dga-stat:nth-child(2) .dga-home-stat-icon {
            background:
                linear-gradient(135deg, var(--dga-theme-secondary), var(--dga-theme-light));
        }

        .dga-home-body .dga-home-panel .dga-stat:nth-child(3) .dga-home-stat-icon {
            background:
                linear-gradient(135deg, var(--dga-theme-accent), var(--dga-theme-secondary));
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-home-panel .dga-stat::before {
            height: 4px;
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-home-panel .dga-stat::after {
            content: "\f19c";
            right: 18px;
            bottom: 18px;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-primary) 12%, #ffffff), color-mix(in srgb, var(--dga-theme-secondary) 16%, #ffffff));
            color: color-mix(in srgb, var(--dga-theme-primary) 56%, #ffffff);
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-size: 16px;
            font-weight: 900;
            line-height: 1;
            opacity: .9;
        }

        .dga-home-body .dga-home-panel .dga-stat:nth-child(2)::after {
            content: "\f0e8";
        }

        .dga-home-body .dga-home-panel .dga-stat:nth-child(3)::after {
            content: "\f46c";
        }

        .dga-home-body .dga-home-panel .dga-stat strong {
            color: var(--dga-theme-primary);
            text-align: left;
        }

        .dga-home-body .dga-home-panel .dga-stat span {
            text-align: left;
        }

        .dga-home-body .dga-overview-panel {
            padding: clamp(18px, 2.4vw, 34px);
            background:
                linear-gradient(135deg, #ffffff 0%, var(--dga-theme-soft) 48%, #ffffff 100%);
        }

        .dga-home-body .dga-overview-panel::before {
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-primary) 10%, transparent), transparent 46%),
                linear-gradient(180deg, rgba(255, 255, 255, .82), transparent 48%);
        }

        .dga-home-body .dga-overview-panel::after {
            top: clamp(88px, 9vw, 118px);
            background:
                linear-gradient(90deg, transparent, var(--dga-theme-primary), var(--dga-theme-secondary), transparent);
            opacity: .22;
        }

        .dga-home-body .dga-overview-panel > h3 {
            min-height: 74px;
            margin-bottom: clamp(18px, 2vw, 28px);
            padding: 18px 24px 18px 86px;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 22%, #ffffff);
            background:
                linear-gradient(135deg, #ffffff, color-mix(in srgb, var(--dga-theme-soft) 78%, #ffffff));
            color: var(--dga-theme-dark);
            box-shadow: 0 18px 42px var(--dga-shadow);
        }

        .dga-home-body .dga-overview-panel > h3::before {
            content: "\f19c";
            left: 22px;
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-family: "Font Awesome 5 Free";
            font-size: 20px;
            font-weight: 900;
            box-shadow: 0 14px 28px var(--dga-theme-shadow);
        }

        .dga-home-body .dga-overview-panel > h3::after {
            content: "Overview";
            background: color-mix(in srgb, var(--dga-theme-accent) 22%, #ffffff);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-overview-panel .dga-hero-card-grid {
            gap: clamp(16px, 2vw, 26px);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card {
            position: relative;
            min-height: 100%;
            padding: 28px 24px 24px;
            overflow: hidden;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-soft) 62%, #ffffff));
            box-shadow: 0 18px 42px var(--dga-shadow);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card::before {
            display: none;
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card::after {
            height: 5px;
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2)::after {
            background: linear-gradient(90deg, var(--dga-theme-accent), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-overview-panel .dga-overview-card-icon {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            border-radius: 14px;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff), color-mix(in srgb, var(--dga-theme-secondary) 18%, #ffffff));
            color: var(--dga-theme-primary);
            font-size: 22px;
            box-shadow: 0 14px 28px color-mix(in srgb, var(--dga-theme-primary) 18%, transparent);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2) .dga-overview-card-icon {
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--dga-theme-accent) 24%, #ffffff), color-mix(in srgb, var(--dga-theme-secondary) 14%, #ffffff));
            color: color-mix(in srgb, var(--dga-theme-dark) 72%, #8b5d12);
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card h4 {
            margin-bottom: 14px;
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-overview-panel .dga-hero-list {
            gap: 10px;
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li {
            min-height: 50px;
            padding: 11px 12px 11px 48px;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 12%, #ffffff);
            background: rgba(255, 255, 255, .82);
            color: var(--dga-muted);
        }

        .dga-home-body .dga-overview-panel .dga-hero-list li::before {
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body .dga-overview-panel .dga-hero-info-card:nth-child(2) .dga-hero-list li::before {
            background: linear-gradient(135deg, var(--dga-theme-accent), var(--dga-theme-secondary));
        }

        .dga-home-body #dga-panel-flow {
            padding: clamp(12px, 1.6vw, 22px);
            background:
                linear-gradient(120deg, rgba(240, 184, 79, .08) 0 18%, transparent 18% 100%),
                linear-gradient(135deg, #fbfdff 0%, #eef8ff 48%, #f4fbf7 100%);
        }

        .dga-home-body #dga-panel-flow > h3 {
            margin-bottom: clamp(10px, 1.3vw, 16px);
            padding: 7px 12px 7px 48px;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 22%, #ffffff);
            background:
                linear-gradient(90deg, #ffffff 0%, #edf7ff 72%, #fff8e6 100%);
            color: var(--dga-theme-dark);
            font-size: clamp(22px, 2vw, 30px);
            line-height: 1.18;
            box-shadow: 0 10px 24px rgba(32, 54, 74, .07);
        }

        .dga-home-body #dga-panel-flow > h3::before {
            left: 14px;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: linear-gradient(135deg, #1f7ec5 0%, #1d7e6f 62%, #d09a2d 100%);
            font-size: 12px;
            box-shadow: 0 8px 16px rgba(29, 126, 111, .18);
        }

	        .dga-home-body .dga-flow-orgmap,
	        .dga-home-body #dga-panel-flow .dga-flow-orgmap {
		            position: relative;
		            --dga-flow-gap: clamp(16px, 2vw, 28px);
		            padding: clamp(14px, 1.8vw, 22px);
		            overflow: hidden;
	            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(120deg, rgba(31, 126, 197, .06) 0 18%, transparent 18% 100%),
                linear-gradient(145deg, #ffffff 0%, #f2faff 48%, #f7fbf4 100%);
            color: var(--dga-ink);
            box-shadow: 0 16px 36px rgba(32, 54, 74, .09);
        }

        .dga-home-body .dga-flow-orgmap::before,
        .dga-home-body #dga-panel-flow .dga-flow-orgmap::before {
            display: block;
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-primary) 5%, transparent), transparent 52%),
                linear-gradient(180deg, rgba(255, 255, 255, .78), transparent 44%);
            pointer-events: none;
        }

        .dga-home-body .dga-flow-org-caption,
        .dga-home-body #dga-panel-flow .dga-flow-org-caption {
            position: relative;
            z-index: 2;
            margin-bottom: clamp(10px, 1.2vw, 16px);
            padding: 0 0 clamp(8px, 1vw, 12px);
            border-bottom: 1px solid var(--dga-line);
        }

        .dga-home-body .dga-flow-org-caption span,
        .dga-home-body #dga-panel-flow .dga-flow-org-caption span {
            color: var(--dga-theme-dark);
            font-size: 12px;
        }

        .dga-home-body .dga-flow-org-caption p,
        .dga-home-body #dga-panel-flow .dga-flow-org-caption p,
        .dga-home-body .dga-flow-column-note,
        .dga-home-body #dga-panel-flow .dga-flow-column-note {
            color: var(--dga-muted);
            font-size: 13px;
            line-height: 1.42;
        }

        .dga-home-body .dga-flow-org-root,
        .dga-home-body #dga-panel-flow .dga-flow-org-root {
            position: relative;
            z-index: 2;
            max-width: 440px;
            margin: 0 auto clamp(22px, 2.8vw, 34px);
            padding: 12px 18px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-accent) 42%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #fff1cd 0%, #ffffff 58%, #eef8ff 100%);
            color: var(--dga-theme-dark);
            font-size: 16px;
            line-height: 1.25;
            box-shadow: 0 18px 38px color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
        }

        .dga-home-body .dga-flow-direction-band,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band {
            position: relative;
            z-index: 1;
            height: 26px;
            margin: -20px auto 4px;
            max-width: 74%;
        }

        .dga-home-body .dga-flow-direction-band::before,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band::before {
            background:
                linear-gradient(90deg, transparent, var(--dga-theme-primary), var(--dga-theme-secondary), transparent);
            opacity: .34;
            box-shadow: none;
        }

        .dga-home-body .dga-flow-direction-band span::before,
        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::before {
            background: var(--dga-theme-primary);
            box-shadow: 0 0 0 6px color-mix(in srgb, var(--dga-theme-primary) 12%, transparent);
        }

        .dga-home-body .dga-flow-org-split,
        .dga-home-body #dga-panel-flow .dga-flow-org-split {
            position: relative;
            z-index: 2;
            gap: clamp(18px, 2.6vw, 34px);
        }

        .dga-home-body .dga-flow-org-split::before,
        .dga-home-body .dga-flow-org-column::before,
        .dga-home-body .dga-flow-org-subgrid::before,
        .dga-home-body .dga-flow-subbranch::before,
        .dga-home-body .dga-flow-child::before,
        .dga-home-body .dga-flow-division-list::before,
        .dga-home-body .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body .dga-flow-field-list::before,
        .dga-home-body .dga-flow-field-list .dga-flow-box::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-split::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-column::before,
        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before,
        .dga-home-body #dga-panel-flow .dga-flow-child::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-division-list .dga-flow-box::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box::before {
            background: color-mix(in srgb, var(--dga-theme-primary) 42%, #ffffff);
            box-shadow: none;
        }

        .dga-home-body .dga-flow-org-column,
        .dga-home-body #dga-panel-flow .dga-flow-org-column {
            padding: clamp(12px, 1.5vw, 18px);
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 14%, #ffffff);
            border-radius: 8px;
            background: rgba(255, 255, 255, .84);
            box-shadow: 0 16px 36px var(--dga-shadow);
        }

        .dga-home-body .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-column.is-hq .dga-flow-org-head,
        .dga-home-body #dga-panel-flow .dga-flow-org-column.is-field .dga-flow-org-head {
            border: 0;
            padding: 9px 12px;
            gap: 6px;
            background: linear-gradient(90deg, #1f7ec5 0%, #1d7e6f 100%);
            color: #ffffff;
            font-size: 15px;
            line-height: 1.22;
            box-shadow: 0 10px 22px rgba(31, 126, 197, .15);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-column.is-field .dga-flow-org-head {
            background: linear-gradient(90deg, #1d7e6f 0%, #2f75b5 64%, #b77c18 100%);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-head::before {
            font-size: 11px;
            color: rgba(255, 255, 255, .78);
        }

        .dga-home-body .dga-flow-box,
        .dga-home-body #dga-panel-flow .dga-flow-box {
            min-height: 0;
            padding: 9px 11px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff 0%, #edf7ff 100%);
            color: var(--dga-theme-dark);
            font-size: 13.5px;
            line-height: 1.3;
            box-shadow: 0 12px 26px var(--dga-shadow);
        }

        .dga-home-body .dga-flow-box.is-blue,
        .dga-home-body .dga-flow-subbranch:first-child > .dga-flow-box.is-blue,
        .dga-home-body .dga-flow-subbranch:last-child > .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-blue,
        .dga-home-body #dga-panel-flow .dga-flow-subbranch:first-child > .dga-flow-box.is-blue,
	        .dga-home-body #dga-panel-flow .dga-flow-subbranch:last-child > .dga-flow-box.is-blue {
            background:
                linear-gradient(135deg, #eaf5ff 0%, #ffffff 100%);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-flow-box.is-green,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-green {
            background:
                linear-gradient(135deg, #e9f8f3 0%, #ffffff 100%);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box.is-link,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link {
            min-height: 56px;
            padding: 10px 11px 10px 14px;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            background:
                linear-gradient(135deg, #ffffff 0%, #f1f8ff 56%, #f8fbf3 100%);
            color: var(--dga-theme-dark);
            box-shadow: 0 10px 22px rgba(32, 54, 74, .08);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:nth-child(even) {
            background: linear-gradient(135deg, #ffffff 0%, #edf8f4 58%, #fff8e9 100%);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:nth-child(3n) {
            background: linear-gradient(135deg, #ffffff 0%, #fff6df 48%, #edf7ff 100%);
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box.is-link::before,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::before {
            background: linear-gradient(180deg, var(--directorate-accent, var(--dga-theme-primary)), var(--dga-theme-accent));
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box.is-link::after,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::after {
            display: none;
            content: none;
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box.is-link .dga-flow-box-copy,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link .dga-flow-box-copy {
            padding-right: 0;
        }

        .dga-home-body .dga-flow-field-list .dga-flow-box.is-link .dga-flow-box-copy strong,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link .dga-flow-box-copy strong {
            font-size: 14px;
            line-height: 1.32;
        }

        .dga-home-body .dga-flow-link-action,
        .dga-home-body #dga-panel-flow .dga-flow-link-action {
            color: var(--dga-theme-primary);
        }

        .dga-home-body .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-mini-tag {
            background: color-mix(in srgb, var(--directorate-accent, var(--dga-theme-primary)) 14%, #ffffff);
            color: var(--dga-theme-dark);
        }

        .dga-home-body .dga-flow-box.is-link i,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link i {
            background: var(--dga-theme-primary);
            color: #ffffff;
        }

        .dga-home-body .dga-flow-box.is-link:hover,
        .dga-home-body .dga-flow-box.is-link:focus,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:hover,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:focus,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:hover,
        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link:focus {
            transform: translateY(-3px) scale(1.01);
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            box-shadow: 0 22px 44px var(--dga-theme-shadow);
        }

        .dga-home-body .dga-flow-box.is-link:hover .dga-flow-link-action,
        .dga-home-body .dga-flow-box.is-link:focus .dga-flow-link-action,
        .dga-home-body .dga-flow-box.is-link:hover .dga-flow-box-copy small,
        .dga-home-body .dga-flow-box.is-link:focus .dga-flow-box-copy small,
        .dga-home-body .dga-flow-box.is-link:hover .dga-flow-mini-tag,
        .dga-home-body .dga-flow-box.is-link:focus .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:hover .dga-flow-link-action,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:focus .dga-flow-link-action,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:hover .dga-flow-box-copy small,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:focus .dga-flow-box-copy small,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:hover .dga-flow-mini-tag,
        .dga-home-body #dga-panel-flow .dga-flow-box.is-link:focus .dga-flow-mini-tag {
            color: rgba(255, 255, 255, .88);
        }

        .dga-home-body #dga-panel-reforms,
        .dga-home-body #dga-panel-modules {
            padding: clamp(18px, 2.4vw, 34px);
            background:
                linear-gradient(135deg, #ffffff 0%, var(--dga-theme-soft) 48%, #ffffff 100%);
        }

        .dga-home-body #dga-panel-reforms .dga-reforms-panel {
            border-color: color-mix(in srgb, var(--dga-theme-primary) 18%, #ffffff);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-soft) 64%, #ffffff));
            box-shadow: 0 18px 42px var(--dga-shadow);
        }

        .dga-home-body #dga-panel-reforms .dga-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
        }

        .dga-home-body #dga-panel-reforms .dga-list li {
            margin: 0;
            border-color: color-mix(in srgb, var(--dga-theme-primary) 14%, #ffffff);
            background: rgba(255, 255, 255, .82);
        }

        .dga-home-body #dga-panel-modules .dga-section-title {
            margin-bottom: clamp(16px, 1.8vw, 24px);
        }

	        .dga-home-body #dga-panel-flow .dga-flow-org-root {
	            margin: 0 auto;
	        }

	        .dga-home-body #dga-panel-flow .dga-flow-org-root::after {
		            top: 100%;
		            height: 20px;
		            background: linear-gradient(180deg, var(--dga-theme-primary), color-mix(in srgb, var(--dga-theme-primary) 42%, #ffffff));
		            box-shadow: none;
		        }

	        .dga-home-body #dga-panel-flow .dga-flow-direction-band {
	            display: grid;
	            grid-template-columns: repeat(2, minmax(0, 1fr));
	            gap: var(--dga-flow-gap);
	            width: 100%;
	            max-width: none;
		            height: 42px;
		            margin: 0 auto;
		        }

	        .dga-home-body #dga-panel-flow .dga-flow-direction-band::before {
	            left: calc((100% - var(--dga-flow-gap)) / 4);
	            right: calc((100% - var(--dga-flow-gap)) / 4);
		            top: 21px;
		            height: 2px;
	            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary));
	            opacity: .75;
	            box-shadow: none;
	        }

	        .dga-home-body #dga-panel-flow .dga-flow-direction-band span {
		            height: 42px;
		        }

	        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::before {
		            top: 21px;
		            height: 21px;
		            width: 2px;
	            background: color-mix(in srgb, var(--dga-theme-primary) 52%, #ffffff);
	            box-shadow: none;
        }

	        .dga-home-body #dga-panel-flow .dga-flow-direction-band span::after {
            content: "";
            position: absolute;
	            left: 50%;
		            bottom: -5px;
		            width: 9px;
		            height: 9px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            background: var(--dga-theme-primary);
            transform: translateX(-50%);
            box-shadow: 0 0 0 5px color-mix(in srgb, var(--dga-theme-primary) 12%, transparent);
        }

	        .dga-home-body #dga-panel-flow .dga-flow-org-split {
	            grid-template-columns: repeat(2, minmax(0, 1fr));
	            gap: var(--dga-flow-gap);
	            margin-top: 0;
	        }

	        .dga-home-body #dga-panel-flow .dga-flow-org-column::before {
		            top: -21px;
		            height: 21px;
		            width: 2px;
	            background: color-mix(in srgb, var(--dga-theme-primary) 52%, #ffffff);
	            box-shadow: none;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-column::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            z-index: 1;
            width: 2px;
            height: clamp(12px, 1.5vw, 18px);
            background: color-mix(in srgb, var(--dga-theme-primary) 52%, #ffffff);
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-head {
            position: relative;
            z-index: 2;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid {
            padding-top: 18px;
            gap: 10px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before {
            top: 8px;
            background: color-mix(in srgb, var(--dga-theme-primary) 42%, #ffffff);
            box-shadow: none;
        }

        .dga-home-body #dga-panel-flow .dga-flow-subbranch {
            padding-top: 18px;
        }

	        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before {
		            height: 18px;
	            background: color-mix(in srgb, var(--dga-theme-primary) 42%, #ffffff);
	            box-shadow: none;
	        }

        .dga-home-body #dga-panel-flow .dga-flow-org-column {
            overflow: visible;
        }

        .dga-home-body #dga-panel-flow .dga-flow-org-subgrid::before {
            left: calc(25% + 1px);
            right: calc(25% + 1px);
            top: 8px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-subbranch::before {
            left: 50%;
            top: 0;
            height: 18px;
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-flow .dga-flow-division-list {
            position: relative;
            margin-top: 16px;
            padding-left: 18px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-division-list::before {
            left: 7px;
            top: -16px;
            bottom: 22px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-division-list .dga-flow-box::before {
            left: -10px;
            right: auto;
            top: 50%;
            width: 10px;
            height: 2px;
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list {
            position: relative;
            margin-top: 16px;
            margin-left: 0;
            padding-left: 20px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list::before {
            left: 8px;
            top: -16px;
            bottom: 28px;
            width: 2px;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::before {
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            height: auto;
            border-radius: 8px 0 0 8px;
            transform: none;
        }

        .dga-home-body #dga-panel-flow .dga-flow-field-list .dga-flow-box.is-link::after {
            display: block;
            content: "";
            position: absolute;
            left: -12px;
            right: auto;
            top: 50%;
            width: 12px;
            height: 2px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: color-mix(in srgb, var(--dga-theme-primary) 42%, #ffffff);
            box-shadow: none;
            transform: translateY(-50%);
        }

	        .dga-home-body #dga-panel-modules .dga-module-grid {
	            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
	        }

        .dga-home-body #dga-panel-reforms .dga-section-title,
        .dga-home-body #dga-panel-modules .dga-section-title {
            position: relative;
            margin-bottom: clamp(18px, 2vw, 28px);
            padding: 18px 22px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff, color-mix(in srgb, var(--dga-theme-soft) 72%, #ffffff));
            box-shadow: 0 14px 32px var(--dga-shadow);
        }

        .dga-home-body #dga-panel-reforms .dga-section-title::before,
        .dga-home-body #dga-panel-modules .dga-section-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            border-radius: 8px 0 0 8px;
            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary));
        }

        .dga-home-body #dga-panel-reforms .dga-section-title span,
        .dga-home-body #dga-panel-modules .dga-section-title span {
            color: var(--dga-theme-primary);
        }

        .dga-home-body #dga-panel-reforms .dga-section-title h3,
        .dga-home-body #dga-panel-modules .dga-section-title h3 {
            color: var(--dga-theme-dark);
        }

        .dga-home-body #dga-panel-reforms .dga-reforms-panel {
            padding: clamp(20px, 2.5vw, 34px);
            overflow: hidden;
        }

        .dga-home-body #dga-panel-reforms .dga-list {
            counter-reset: reformStep;
        }

        .dga-home-body #dga-panel-reforms .dga-list li {
            position: relative;
            min-height: 88px;
            display: flex;
            align-items: center;
            padding: 16px 18px 16px 68px;
            overflow: hidden;
            box-shadow: 0 12px 28px var(--dga-shadow);
        }

        .dga-home-body #dga-panel-reforms .dga-list li::before {
            counter-increment: reformStep;
            content: counter(reformStep);
            position: absolute;
            left: 18px;
            top: 50%;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 10px 20px var(--dga-theme-shadow);
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-reforms .dga-list li::after {
            content: "";
            position: absolute;
            right: -28px;
            bottom: -30px;
            width: 90px;
            height: 90px;
            border-radius: 18px;
            background: color-mix(in srgb, var(--dga-theme-primary) 9%, transparent);
            transform: rotate(18deg);
        }

        .dga-home-body #dga-panel-modules .dga-module-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: clamp(14px, 1.8vw, 22px);
            padding: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n) {
            min-height: 100%;
            padding: 24px 22px 22px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 16%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), color-mix(in srgb, var(--dga-theme-soft) 58%, #ffffff));
            box-shadow: 0 16px 36px var(--dga-shadow);
            transform: none;
        }

        .dga-home-body #dga-panel-modules .dga-module::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::before {
            height: 5px;
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
        }

        .dga-home-body #dga-panel-modules .dga-module::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::after {
            right: 18px;
            bottom: 18px;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: color-mix(in srgb, var(--dga-theme-primary) 9%, transparent);
        }

        .dga-home-body #dga-panel-modules .dga-module:hover,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(n):hover {
            transform: translateY(-4px);
            border-color: color-mix(in srgb, var(--dga-theme-primary) 32%, #ffffff);
            box-shadow: 0 22px 44px var(--dga-theme-shadow);
        }

        .dga-home-body #dga-panel-modules .dga-module-identity {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            box-shadow: 0 14px 28px var(--dga-theme-shadow);
        }

        .dga-home-body #dga-panel-modules .dga-module-label {
            border-color: color-mix(in srgb, var(--dga-theme-accent) 36%, #ffffff);
            background: color-mix(in srgb, var(--dga-theme-accent) 18%, #ffffff);
            color: var(--dga-theme-dark);
        }

        .dga-home-body #dga-panel-modules .dga-module h4 {
            color: var(--dga-theme-dark);
            font-size: 18px;
            line-height: 1.25;
        }

        .dga-home-body #dga-panel-modules .dga-module ul {
            gap: 8px;
            margin-top: 14px;
        }

        .dga-home-body #dga-panel-modules .dga-module li {
            position: relative;
            padding-left: 22px;
            color: var(--dga-muted);
            line-height: 1.45;
        }

        .dga-home-body #dga-panel-modules .dga-module li::before {
            content: "";
            position: absolute;
            left: 0;
            top: .58em;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--dga-theme-primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--dga-theme-primary) 10%, transparent);
        }

        .dga-home-body #dga-panel-modules .dga-module-grid {
            counter-reset: moduleFlow;
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            padding-left: clamp(18px, 2.8vw, 34px);
        }

        .dga-home-body #dga-panel-modules .dga-module-grid::before {
            content: "";
            position: absolute;
            left: clamp(39px, 4.7vw, 58px);
            top: 18px;
            bottom: 18px;
            width: 3px;
            border-radius: 999px;
            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
            opacity: .32;
        }

        .dga-home-body #dga-panel-modules .dga-module,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n) {
            counter-increment: moduleFlow;
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            column-gap: 18px;
            align-items: start;
            min-height: 118px;
            padding: 20px 22px 20px 20px;
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff, color-mix(in srgb, var(--dga-theme-soft) 62%, #ffffff));
            box-shadow: 0 14px 34px var(--dga-shadow);
        }

        .dga-home-body #dga-panel-modules .dga-module::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::before {
            content: "";
            position: absolute;
            left: -22px;
            top: 36px;
            width: 22px;
            height: 3px;
            border-radius: 999px;
            background: var(--dga-theme-primary);
            opacity: .55;
        }

        .dga-home-body #dga-panel-modules .dga-module::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::after {
            content: counter(moduleFlow, decimal-leading-zero);
            left: -50px;
            top: 20px;
            right: auto;
            bottom: auto;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 12px 24px var(--dga-theme-shadow);
            transform: none;
        }

        .dga-home-body #dga-panel-modules .dga-module-identity {
            grid-row: 1 / span 3;
            width: 56px;
            height: 56px;
            margin: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module-label {
            width: max-content;
            margin: 0 0 8px;
            font-size: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module-label::after {
            content: "Module Step";
            font-size: 10px;
        }

        .dga-home-body #dga-panel-modules .dga-module h4 {
            margin: 0 0 10px;
            font-size: clamp(17px, 1.35vw, 21px);
        }

        .dga-home-body #dga-panel-modules .dga-module ul {
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 8px 12px;
        }

        .dga-home-body #dga-panel-modules .dga-module li {
            min-height: 34px;
            padding: 7px 10px 7px 28px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 10%, #ffffff);
            border-radius: 8px;
            background: rgba(255, 255, 255, .74);
        }

        .dga-home-body #dga-panel-modules .dga-module li::before {
            left: 10px;
            top: 50%;
            width: 8px;
            height: 8px;
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-modules .dga-module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: clamp(14px, 1.8vw, 22px);
            padding-left: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module-grid::before {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-modules .dga-module,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n) {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            grid-template-areas:
                "icon label"
                "icon title"
                "details details";
            column-gap: 16px;
            row-gap: 8px;
            min-height: 0;
            padding: 22px;
            border-left: 6px solid var(--dga-theme-primary);
        }

        .dga-home-body #dga-panel-modules .dga-module::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::before,
        .dga-home-body #dga-panel-modules .dga-module::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::after {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-modules .dga-module-identity {
            grid-area: icon;
            align-self: start;
            width: 54px;
            height: 54px;
            margin: 0;
            border-radius: 16px;
        }

        .dga-home-body #dga-panel-modules .dga-module-label {
            grid-area: label;
            align-self: end;
            margin: 0;
            font-size: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module-label::after {
            content: "Module";
            font-size: 10px;
        }

        .dga-home-body #dga-panel-modules .dga-module h4 {
            grid-area: title;
            margin: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module ul {
            grid-area: details;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .dga-home-body #dga-panel-modules .dga-module li {
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            width: auto;
            max-width: 100%;
            padding: 7px 11px 7px 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .82);
        }

        .dga-home-body #dga-panel-modules .dga-module-grid {
            counter-reset: dgaModuleStep;
            display: grid;
            grid-template-columns: repeat(4, minmax(195px, 1fr));
            gap: 48px 44px;
            min-height: 0;
            padding: 10px 8px 16px;
            overflow: visible;
        }

        .dga-home-body #dga-panel-modules .dga-module-grid::before {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-modules .dga-module,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even),
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n) {
            position: relative;
            z-index: 1;
            counter-increment: dgaModuleStep;
            width: 100%;
            min-height: 146px;
            max-height: none;
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            grid-template-areas:
                "icon label"
                "icon title"
                "details details";
            column-gap: 12px;
            row-gap: 6px;
            padding: 14px;
            overflow: visible;
            border-left: 0;
            border-top: 4px solid var(--dga-theme-primary);
            border-radius: 8px;
            background: linear-gradient(180deg, #ffffff, #f7fbff);
            box-shadow: 0 14px 28px rgba(19, 67, 108, .1);
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(even) {
            align-self: stretch;
            margin-top: 0;
            border-top-color: var(--dga-theme-secondary);
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd) {
            align-self: stretch;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(-n + 4) {
            grid-row: 1;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(n + 5) {
            grid-row: 2;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(5) {
            grid-column: 4;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(6) {
            grid-column: 3;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(7) {
            grid-column: 2;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(8) {
            grid-column: 1;
        }

        .dga-home-body #dga-panel-modules .dga-module::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::before {
            display: block;
            content: "";
            position: absolute;
            left: 100%;
            top: 50%;
            z-index: 2;
            width: 44px;
            height: 2px;
            border-radius: 999px;
            background:
                linear-gradient(90deg, color-mix(in srgb, var(--dga-theme-primary) 22%, transparent), var(--dga-theme-primary), var(--dga-theme-secondary));
            opacity: .78;
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-modules .dga-module::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::after {
            display: block;
            content: "\f061";
            position: absolute;
            left: calc(100% + 9px);
            top: 50%;
            z-index: 2;
            width: 26px;
            height: 20px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-primary) 22%, #ffffff);
            border-radius: 999px;
            background:
                linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-primary) 8%, #ffffff) 54%, color-mix(in srgb, var(--dga-theme-secondary) 14%, #ffffff) 100%);
            color: var(--dga-theme-primary);
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-size: 11px;
            font-weight: 900;
            line-height: 18px;
            text-align: center;
            box-shadow:
                0 8px 18px color-mix(in srgb, var(--dga-theme-primary) 14%, transparent),
                inset 0 0 0 3px rgba(255, 255, 255, .72);
            transform: translateY(-50%);
        }

        .dga-home-body #dga-panel-modules .dga-module:last-child::before,
        .dga-home-body #dga-panel-modules .dga-module:last-child::after {
            display: none;
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(4)::before {
            left: 50%;
            top: 100%;
            width: 2px;
            height: 48px;
            background: linear-gradient(180deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(4)::after {
            display: block;
            content: "\f063";
            left: 50%;
            right: auto;
            top: auto;
            bottom: -34px;
            width: 26px;
            height: 20px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-secondary) 24%, #ffffff);
            border-radius: 999px;
            background:
                linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-secondary) 12%, #ffffff) 100%);
            color: var(--dga-theme-secondary);
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-size: 11px;
            font-weight: 900;
            line-height: 18px;
            text-align: center;
            box-shadow:
                0 8px 18px color-mix(in srgb, var(--dga-theme-secondary) 14%, transparent),
                inset 0 0 0 3px rgba(255, 255, 255, .72);
            transform: translateX(-50%);
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(5)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(6)::before,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(7)::before {
            left: auto;
            right: 100%;
            background:
                linear-gradient(90deg, var(--dga-theme-secondary), var(--dga-theme-primary), color-mix(in srgb, var(--dga-theme-primary) 22%, transparent));
        }

        .dga-home-body #dga-panel-modules .dga-module:nth-child(5)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(6)::after,
        .dga-home-body #dga-panel-modules .dga-module:nth-child(7)::after {
            content: "\f060";
            left: auto;
            right: calc(100% + 9px);
            width: 26px;
            height: 20px;
            border: 1px solid color-mix(in srgb, var(--dga-theme-secondary) 24%, #ffffff);
            border-radius: 999px;
            background:
                linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--dga-theme-secondary) 8%, #ffffff) 54%, color-mix(in srgb, var(--dga-theme-primary) 14%, #ffffff) 100%);
            color: var(--dga-theme-secondary);
            font-family: "Font Awesome 5 Free", "FontAwesome";
            font-size: 11px;
            font-weight: 900;
            line-height: 18px;
            text-align: center;
            box-shadow:
                0 8px 18px color-mix(in srgb, var(--dga-theme-secondary) 14%, transparent),
                inset 0 0 0 3px rgba(255, 255, 255, .72);
        }

        .dga-home-body #dga-panel-modules .dga-module-identity {
            position: relative;
            left: auto;
            top: auto;
            grid-area: icon;
            align-self: start;
            margin: 0;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 22px;
            line-height: 1;
        }

        .dga-home-body #dga-panel-modules .dga-module-identity i {
            font-size: 22px;
            line-height: 1;
        }

        .dga-home-body #dga-panel-modules .dga-module-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0;
        }

        .dga-home-body #dga-panel-modules .dga-module-label::before {
            content: counter(dgaModuleStep, decimal-leading-zero);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 23px;
            height: 16px;
            padding: 0 5px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
        }

        .dga-home-body #dga-panel-modules .dga-module-label::after {
            content: none;
            display: none;
        }

        .dga-home-body .dga-module-label {
            font-size: 0;
        }

        .dga-home-body .dga-module-label::after {
            content: none;
            display: none;
        }

        .dga-home-body #dga-panel-modules .dga-module h4 {
            font-size: 15.5px;
            line-height: 1.16;
        }

        .dga-home-body #dga-panel-modules .dga-module ul {
            max-height: none;
            gap: 5px;
            overflow: visible;
        }

        .dga-home-body #dga-panel-modules .dga-module li {
            min-height: 24px;
            padding: 5px 9px 5px 22px;
            font-size: 11px;
            line-height: 1.22;
        }

        .dga-home-body #dga-panel-modules .dga-section-title {
            margin-bottom: 8px;
            padding: 8px 10px;
        }

        .dga-home-body #dga-panel-modules .dga-section-title span {
            font-size: 10px;
        }

        .dga-home-body #dga-panel-modules .dga-section-title h3 {
            font-size: 20px;
            line-height: 1.15;
        }

	        .dga-home-body #dga-panel-home.dga-home-panel {
	            min-height: clamp(255px, 32vh, 350px);
	            display: flex;
	            align-items: center;
	            justify-content: flex-start;
	            padding: clamp(18px, 2.4vw, 30px) !important;
	            overflow: hidden;
	            isolation: isolate;
	            background: linear-gradient(115deg, #f8fcff 0%, #eff7fb 48%, #e9f4f7 100%);
        }

        .dga-home-body #dga-panel-home.dga-home-panel:not(.is-active) {
            display: none;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::before,
        .dga-home-body #dga-panel-home.dga-home-panel::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::before {
            left: 0;
            width: 52%;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .14), rgba(248, 252, 255, .76)),
                url("{{ asset('site/image/background.jpg') }}") left center / cover no-repeat;
            clip-path: none;
            opacity: .7;
        }

        .dga-home-body #dga-panel-home.dga-home-panel::after {
            right: 0;
            width: 60%;
            background:
                linear-gradient(270deg, rgba(7, 45, 78, .36) 0%, rgba(37, 111, 153, .2) 42%, rgba(248, 252, 255, .54) 100%),
                url("{{ asset('site/image/hero-bg.png') }}") right center / cover no-repeat;
            clip-path: none;
            opacity: 1;
        }

        .dga-home-body #dga-panel-home .dga-home-intro {
            position: relative;
            z-index: 2;
            width: min(72%, 820px);
	            min-height: clamp(112px, 13vh, 150px);
	            display: flex;
	            flex-direction: column;
	            justify-content: center;
	            gap: clamp(8px, 1vw, 12px);
	            margin: 0;
	            padding: clamp(16px, 2vw, 24px) clamp(20px, 2.6vw, 34px);
	            overflow: hidden;
	            border: 1px solid rgba(255, 255, 255, .78);
	            border-radius: 14px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .94) 0%, rgba(248, 252, 255, .86) 56%, rgba(232, 245, 248, .74) 100%);
            box-shadow:
                0 22px 48px rgba(20, 74, 111, .18),
                inset 0 1px 0 rgba(255, 255, 255, .92);
            backdrop-filter: blur(12px);
        }

        .dga-home-body #dga-panel-home .dga-home-intro::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--dga-theme-primary), var(--dga-theme-secondary), var(--dga-theme-accent));
            box-shadow: 0 8px 20px rgba(47, 117, 181, .16);
        }

        .dga-home-body #dga-panel-home .dga-stat-grid {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro::after {
            content: "";
            position: absolute;
            right: clamp(18px, 3vw, 44px);
            bottom: clamp(14px, 2.4vw, 32px);
            width: clamp(56px, 6vw, 84px);
            height: clamp(56px, 6vw, 84px);
            border-radius: 50%;
            background:
                radial-gradient(circle, rgba(47, 117, 181, .12), transparent 63%),
                conic-gradient(from 120deg, rgba(47, 117, 181, .14), rgba(40, 137, 144, .12), rgba(240, 184, 79, .14), rgba(47, 117, 181, .14));
            opacity: .68;
            pointer-events: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro h2 {
            position: relative;
            z-index: 1;
            max-width: 760px;
            margin: 0;
            color: #0e4775;
	            font-size: clamp(19px, 1.75vw, 27px);
	            line-height: 1.1;
	            letter-spacing: 0;
	            text-shadow: none;
        }

        .dga-home-body #dga-panel-home .dga-home-intro p {
            position: relative;
            z-index: 1;
            max-width: 760px;
            margin: 0;
	            padding: clamp(7px, .85vw, 10px) clamp(10px, 1.2vw, 13px);
	            border-left: 4px solid rgba(40, 137, 144, .72);
	            border-radius: 10px;
	            background: rgba(239, 248, 251, .78);
	            color: #2f4960;
	            font-size: clamp(11px, .82vw, 13px);
	            line-height: 1.42;
	            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .78);
	        }

        .dga-home-body #dga-panel-home.dga-home-panel::before,
        .dga-home-body #dga-panel-home.dga-home-panel::after {
            display: none;
            content: none;
        }

        .dga-home-body #dga-panel-home .dga-home-visual {
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 0;
            display: block;
            pointer-events: none;
        }

        .dga-home-body #dga-panel-home .dga-home-visual-left {
            left: 0;
            width: 54%;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .28), rgba(232, 244, 250, .62)),
                url("{{ asset('site/image/background.jpg') }}") left center / cover no-repeat;
            opacity: .74;
        }

        .dga-home-body #dga-panel-home .dga-home-visual-right {
            right: 0;
            width: 54%;
            background:
                linear-gradient(270deg, rgba(7, 45, 78, .28), rgba(232, 244, 250, .62)),
                url("{{ asset('site/image/hero-bg.png') }}") right center / cover no-repeat;
            opacity: .74;
        }

        .dga-home-body #dga-panel-home .dga-home-visual-right::before {
            content: "";
            position: absolute;
            left: -80px;
            top: 0;
            bottom: 0;
            width: 160px;
            border-radius: 0;
            background: linear-gradient(90deg, rgba(232, 244, 250, .86), rgba(232, 244, 250, .28), rgba(232, 244, 250, .86));
            filter: blur(10px);
            pointer-events: none;
        }

        .dga-home-body .dga-department-strip {
            border-top: 0;
            background:
                linear-gradient(90deg, #24689f 0%, #207982 52%, #2c7b6a 100%);
            box-shadow: 0 9px 22px rgba(13, 66, 96, .2);
        }

        .dga-home-body .dga-department-strip::before {
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .12), transparent 34% 72%, rgba(255, 244, 199, .12));
        }

        .dga-home-body .dga-department-strip::after {
            height: 2px;
            background: linear-gradient(90deg, #f0b84f, rgba(255, 255, 255, .74), #9be0d5);
            opacity: .8;
        }

        .dga-home-body .dga-department-grid {
            justify-content: flex-start;
	            gap: clamp(10px, 1.8vw, 24px);
	            padding: 7px 0 8px clamp(8px, 1.8vw, 26px);
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
	            min-height: 32px;
	            min-width: auto;
	            padding: 6px 3px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: rgba(255, 255, 255, .9);
            box-shadow: none;
            backdrop-filter: none;
	            gap: 6px;
        }

        .dga-home-body .dga-department-title::before {
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

        .dga-home-body .dga-department-title strong {
	            font-size: 13px;
            letter-spacing: .04em;
        }

        .dga-home-body .dga-department-arrow {
            display: inline-flex;
	            width: 16px;
	            height: 16px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .9);
	            font-size: 10px;
            transition: transform .22s ease, background .22s ease, color .22s ease;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: transparent;
            border-color: transparent;
            color: #ffffff;
            box-shadow: none;
            transform: translateY(-1px);
        }

        .dga-home-body .dga-department-title:hover::before,
        .dga-home-body .dga-department-title:focus::before,
        .dga-home-body .dga-department-item.is-open .dga-department-title::before {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-home-body .dga-department-item:hover .dga-department-arrow,
        .dga-home-body .dga-department-item:focus-within .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: #fff4c7;
            color: #174f82;
            transform: rotate(180deg);
        }

	        @media (max-width: 991px) {
	            .dga-home-body #dga-panel-home.dga-home-panel {
	                min-height: 210px;
	                padding: 12px !important;
	                background: linear-gradient(115deg, #f8fcff 0%, #eef7fb 100%);
	            }

            .dga-home-body #dga-panel-home .dga-home-visual-left {
                left: 0;
                width: 58%;
                clip-path: none;
                opacity: .38;
            }

            .dga-home-body #dga-panel-home .dga-home-visual-right {
                top: 0;
                right: 0;
                bottom: 0;
                width: 58%;
                clip-path: none;
                opacity: .38;
            }

            .dga-home-body #dga-panel-home .dga-home-visual-right::before {
                left: -48px;
                width: 96px;
            }

	            .dga-home-body #dga-panel-home .dga-home-intro {
	                width: 100%;
	                min-height: 0;
	                display: flex;
	                margin-left: 0;
	                padding: 16px 14px;
	            }

	            .dga-home-body #dga-panel-home .dga-home-intro h2 {
	                margin-bottom: 8px;
	                font-size: clamp(18px, 4vw, 24px);
	            }

	            .dga-home-body #dga-panel-home .dga-home-intro p {
	                padding: 9px 10px;
	                border-left-width: 3px;
	            }

            .dga-home-body #dga-panel-modules .dga-module-grid {
                padding-left: 0;
                grid-template-columns: 1fr;
                grid-template-rows: none;
                gap: 14px;
            }

            .dga-home-body #dga-panel-modules .dga-module-grid::before,
            .dga-home-body #dga-panel-modules .dga-module::before,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::before,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::before,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::before,
            .dga-home-body #dga-panel-modules .dga-module::after,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(odd)::after,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(even)::after,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(3n)::after {
                display: none;
            }

            .dga-home-body #dga-panel-modules .dga-module,
            .dga-home-body #dga-panel-modules .dga-module:nth-child(odd),
            .dga-home-body #dga-panel-modules .dga-module:nth-child(even),
            .dga-home-body #dga-panel-modules .dga-module:nth-child(3n) {
                grid-template-columns: 52px minmax(0, 1fr);
                width: 100%;
                align-self: stretch;
                grid-column: auto;
                grid-row: auto;
            }

            .dga-home-body .dga-home-panel .dga-home-intro {
                min-height: 190px;
                padding-left: 42px;
            }

            .dga-home-body .dga-home-panel .dga-home-intro::after,
            .dga-home-body .dga-home-panel .dga-stat-grid::before {
                display: none;
            }

            .dga-home-body .dga-flow-direction-band,
            .dga-home-body .dga-flow-org-root::after,
            .dga-home-body .dga-flow-org-split::before,
            .dga-home-body .dga-flow-org-column::before,
            .dga-home-body .dga-flow-org-subgrid::before,
            .dga-home-body .dga-flow-subbranch::before,
            .dga-home-body .dga-flow-child::before,
            .dga-home-body .dga-flow-division-list::before,
            .dga-home-body .dga-flow-division-list .dga-flow-box::before,
            .dga-home-body .dga-flow-field-list::before,
            .dga-home-body .dga-flow-field-list .dga-flow-box::before {
                display: none;
            }

            .dga-home-body .dga-department-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                justify-content: stretch;
                overflow: visible;
            }

            .dga-home-body .dga-department-title,
            .dga-home-body .dga-department-title:hover,
            .dga-home-body .dga-department-title:focus {
                width: 100%;
                justify-content: center;
                border-radius: 8px;
            }

            .dga-home-body .dga-department-menu {
                left: 0;
                top: auto;
                width: 100%;
                min-width: 0;
                margin-top: 8px;
                transform: translateY(6px);
            }

            .dga-home-body .dga-department-menu::before {
                left: 26px;
            }

            .dga-home-body .dga-department-item:hover .dga-department-menu,
            .dga-home-body .dga-department-item:focus-within .dga-department-menu,
            .dga-home-body .dga-department-item.is-open .dga-department-menu {
                transform: translateY(0);
            }

            .dga-home-body .dga-overview-panel .dga-hero-card-grid {
                grid-template-columns: 1fr;
            }

            .dga-home-body .dga-bottom-updates::before {
                position: static;
                display: inline-flex;
                width: max-content;
                margin: 0 0 12px;
            }

		        .dga-home-body .dga-bottom-updates {
		            grid-template-columns: 1fr;
		            gap: 6px;
		            margin-top: 6px;
		            padding: 6px;
		        }

		        .dga-home-body .dga-update-photo-frame {
		            min-height: 78px;
		        }

		        .dga-home-body .dga-bottom-updates-window {
		            height: 92px;
		        }

            .dga-home-body .dga-home-footer-strip {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 8px;
                padding: 10px;
            }

            .dga-home-body .dga-footer-credit,
            .dga-home-body .dga-home-footer-center,
            .dga-home-body .dga-home-footer-meta {
                min-height: 0;
            }

            .dga-home-body .dga-footer-credit {
                flex-direction: row;
                align-items: center;
                justify-content: center;
            }

            .dga-home-body .dga-footer-top-link {
                right: 18px;
                bottom: 18px;
            }
        }

		        @media (max-width: 575px) {
		            .dga-home-body .dga-update-photo-frame {
		                min-height: 62px;
		            }

		            .dga-home-body .dga-bottom-updates-head {
		                min-height: 34px;
		                padding: 5px 8px;
		            }

		            .dga-home-body .dga-bottom-updates-head h3 {
		                font-size: 14px;
		            }

		            .dga-home-body .dga-bottom-updates-window {
		                height: 82px;
		                padding: 5px;
		            }

            .dga-home-body .dga-home-footer-strip {
                width: 100%;
                margin: 8px 0 0;
                padding: 10px;
                border-radius: 8px 8px 0 0;
            }

            .dga-home-body .dga-footer-credit,
            .dga-home-body .dga-home-footer-center,
            .dga-home-body .dga-home-footer-meta {
                min-height: 0;
                padding: 10px 12px;
            }

            .dga-home-body .dga-footer-credit {
                flex-direction: row;
                align-items: center;
                justify-content: center;
                gap: 4px;
            }

            .dga-home-body .dga-footer-credit span,
            .dga-home-body .dga-footer-credit span strong {
                white-space: normal;
                text-align: center;
                font-size: 14px;
            }

            .dga-home-body .dga-home-footer-heading {
                align-items: flex-start;
                gap: 8px;
            }

            .dga-home-body .dga-footer-top-link {
                right: 14px;
                bottom: 14px;
                width: 38px;
                height: 38px;
            }

            .dga-home-body .dga-footer-top-link i {
                width: auto;
                height: auto;
                font-size: 14px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dga-home-body .dga-department-menu,
            .dga-home-body .dga-department-menu a {
                transition: none;
            }

            .dga-home-body .dga-bottom-updates-window .dga-updates-track {
                animation: none;
            }
        }
        .dga-home-body .dga-department-strip {
            border-top: 1px solid color-mix(in srgb, var(--dga-theme-light) 20%, transparent);
            border-bottom: 1px solid color-mix(in srgb, var(--dga-theme-dark) 36%, transparent);
            box-shadow: 0 8px 18px rgba(13, 66, 96, .16);
        }

        .dga-home-body .dga-department-strip::before {
            display: none;
        }

        .dga-home-body .dga-department-strip::after {
            height: 1px;
            opacity: .65;
        }

        .dga-home-body .dga-department-strip .container {
            max-width: none;
            padding-left: clamp(18px, 3vw, 54px);
            padding-right: clamp(18px, 3vw, 54px);
        }

        .dga-home-body .dga-department-grid {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 0;
            padding: 0;
            overflow: visible;
        }

        .dga-home-body .dga-department-item {
            position: relative;
            flex: 0 0 auto;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
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

        .dga-home-body .dga-department-item:last-child .dga-department-title {
            border-right: 1px solid color-mix(in srgb, #ffffff 14%, transparent);
        }

        .dga-home-body .dga-department-title::before {
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            border-radius: 0;
            opacity: 0;
            transform: scaleX(0);
        }

        .dga-home-body .dga-department-title strong {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .dga-home-body .dga-department-arrow {
            width: auto;
            height: auto;
            border-radius: 0;
            background: transparent;
            font-size: 10px;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-title.is-active,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: color-mix(in srgb, var(--dga-theme-accent) 24%, transparent);
            color: #ffffff;
            box-shadow: inset 0 -3px 0 var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-title:hover::before,
        .dga-home-body .dga-department-title:focus::before,
        .dga-home-body .dga-department-title.is-active::before,
        .dga-home-body .dga-department-item.is-open .dga-department-title::before {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-home-body .dga-department-item:hover .dga-department-arrow,
        .dga-home-body .dga-department-item:focus-within .dga-department-arrow,
        .dga-home-body .dga-department-title.is-active .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            background: transparent;
            color: inherit;
            transform: rotate(180deg);
        }

        .dga-home-body .dga-department-menu {
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

        .dga-home-body .dga-department-menu::before,
        .dga-home-body .dga-department-menu a::before {
            display: none;
        }

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
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

        .dga-home-body .dga-department-menu a:last-child {
            border-bottom: 0;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            background: color-mix(in srgb, var(--dga-theme-accent) 18%, transparent);
            color: #ffffff;
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-department-strip .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .dga-home-body .dga-department-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1px;
            }

            .dga-home-body .dga-department-title,
            .dga-home-body .dga-department-title:hover,
            .dga-home-body .dga-department-title:focus {
                width: 100%;
                min-height: 38px;
                padding: 0 12px;
                border: 0;
                justify-content: center;
            }

            .dga-home-body .dga-department-menu {
                position: static;
                width: 100%;
                min-width: 0;
                margin-top: 0;
                transform: none;
            }
        }
        .dga-home-body .dga-nav {
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

        .dga-home-body .dga-nav-link,
        .dga-home-body .dga-nav-link:hover,
        .dga-home-body .dga-nav-link:focus {
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

        .dga-home-body .dga-nav-link:last-child {
            border-right: 1px solid rgba(47, 117, 181, .18);
        }

        .dga-home-body .dga-nav-link i {
            width: 15px;
            text-align: center;
            color: var(--dga-theme-accent);
            font-size: .82rem;
        }

        .dga-home-body .dga-nav-link:hover,
        .dga-home-body .dga-nav-link:focus {
            background: var(--dga-theme-soft);
            color: var(--dga-theme-primary-dark);
        }

        .dga-home-body .dga-nav-link.primary,
        .dga-home-body .dga-nav-link.primary:hover,
        .dga-home-body .dga-nav-link.primary:focus {
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-dark));
            color: #fff;
            box-shadow: 0 12px 24px rgba(13, 45, 73, .18);
        }

        .dga-home-body .dga-nav-link.primary i {
            color: var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-menu {
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

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
            min-height: 42px;
            padding: 10px 18px 10px 16px;
            justify-content: flex-start;
            text-align: left;
            line-height: 1.25;
            border-bottom: 1px solid rgba(255, 255, 255, .11);
            box-shadow: none;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            padding-left: 20px;
            background: var(--dga-theme-primary-dark);
            box-shadow: inset 4px 0 0 var(--dga-theme-accent);
        }

        .dga-home-body .dga-theme-switch {
            gap: 7px;
            padding: 4px 6px;
            border-color: rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .18), 0 8px 18px rgba(5, 31, 55, .12);
        }

        .dga-home-body .dga-theme-switch button {
            position: relative;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
            box-shadow: none;
            transform: none;
        }

        .dga-home-body .dga-theme-switch button::before {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, .9);
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .08);
        }

        .dga-home-body .dga-theme-switch button::after {
            content: "\f00c";
            position: absolute;
            right: -2px;
            bottom: -2px;
            width: 13px;
            height: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #08213f;
            background: #ffffff;
            font-family: "Font Awesome 5 Free";
            font-size: 8px;
            font-weight: 900;
            opacity: 0;
            transform: scale(.6);
            transition: opacity .18s ease, transform .18s ease;
        }

        .dga-home-body .dga-theme-switch button:hover,
        .dga-home-body .dga-theme-switch button:focus {
            border-color: rgba(255, 244, 199, .88);
            background: rgba(255, 255, 255, .18);
            box-shadow: 0 0 0 3px rgba(255, 244, 199, .16);
            transform: translateY(-1px);
        }

        .dga-home-body .dga-theme-switch button.is-active {
            border-color: #fff4c7;
            background: rgba(255, 255, 255, .26);
            box-shadow: 0 0 0 3px rgba(255, 244, 199, .2), 0 8px 18px rgba(5, 31, 55, .18);
            transform: translateY(-1px);
        }

        .dga-home-body .dga-theme-switch button.is-active::after {
            opacity: 1;
            transform: scale(1);
        }

        .dga-home-body .dga-header .container,
        .dga-home-body .dga-department-strip .container {
            width: 100%;
            max-width: none;
            /* margin-top: 36px; */
            margin-right: 0;
            padding-left: clamp(18px, 2.6vw, 52px);
            padding-right: clamp(18px, 2.6vw, 52px);
        }

        .dga-home-body .dga-header .row {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .dga-home-body .dga-header .row > [class*="col-"] {
            padding-left: 0;
            padding-right: 0;
        }

        .dga-home-body .dga-department-grid {
            justify-content: flex-start;
            gap: 9px;
            padding: 2px 0;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
            min-width: 82px;
            min-height: 38px;
            padding: 8px 15px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 8px;
            background: rgba(255, 255, 255, .1);
            color: #ffffff;
            box-shadow: none;
            backdrop-filter: blur(10px);
        }

        .dga-home-body .dga-department-title strong {
            color: inherit;
            font-size: 13px;
            font-weight: 900;
        }

        .dga-home-body .dga-department-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff4c7;
            font-size: 10px;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: #ffffff;
            border-color: rgba(240, 184, 79, .72);
            color: var(--dga-theme-dark);
            box-shadow: 0 10px 20px rgba(7, 30, 49, .16);
            transform: translateY(-1px);
        }

        .dga-home-body .dga-department-title:hover .dga-department-arrow,
        .dga-home-body .dga-department-title:focus .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            color: var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-menu {
            min-width: 280px;
            padding: 8px;
            border: 1px solid rgba(47, 117, 181, .18);
            border-top: 4px solid var(--dga-theme-accent);
            border-left: 0;
            border-radius: 0 0 10px 10px;
            background: linear-gradient(180deg, #ffffff 0%, #f4fbff 100%);
            box-shadow: 0 18px 36px rgba(7, 30, 49, .18);
        }

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
            min-height: 40px;
            margin-bottom: 4px;
            padding: 9px 13px 9px 45px;
            border: 1px solid transparent;
            border-radius: 7px;
            background: transparent;
            color: #174f82;
            font-size: 12px;
            font-weight: 800;
            text-transform: none;
            letter-spacing: 0;
        }

        .dga-home-body .dga-department-menu a::before {
            left: 10px;
            width: 24px;
            height: 24px;
            background: #e7f7f2;
            color: #146a5e;
            box-shadow: none;
        }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
            padding-left: 45px;
            border-color: rgba(47, 117, 181, .18);
            background: linear-gradient(90deg, #eaf6ff 0%, #edf9f5 100%);
            color: #0b3151;
            transform: translateX(2px);
            box-shadow: inset 3px 0 0 var(--dga-theme-accent);
        }

        .dga-home-body .dga-department-menu a:hover::before,
        .dga-home-body .dga-department-menu a:focus::before {
            background: var(--dga-theme-accent);
            color: #0b3151;
        }

        .dga-home-body .dga-department-grid {
            gap: clamp(16px, 2.4vw, 34px);
            padding: 0 360px;
            position: relative;
        }

        .dga-home-body .dga-department-title,
        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus {
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

        .dga-home-body .dga-department-title::before,
        .dga-home-body .dga-department-title::after {
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

        .dga-home-body .dga-department-title strong {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0;
        }

        .dga-home-body .dga-department-arrow {
            color: rgba(255, 244, 199, .9);
            opacity: .72;
            transition: transform .2s ease, opacity .2s ease, color .2s ease;
        }

        .dga-home-body .dga-department-title:hover,
        .dga-home-body .dga-department-title:focus,
        .dga-home-body .dga-department-title.is-active,
        .dga-home-body .dga-department-item.is-open .dga-department-title {
            background: transparent;
            border-color: transparent;
            color: #ffffff;
            box-shadow: none;
            transform: none;
        }

        .dga-home-body .dga-department-title:hover::before,
        .dga-home-body .dga-department-title:focus::before,
        .dga-home-body .dga-department-title.is-active::before,
        .dga-home-body .dga-department-item.is-open .dga-department-title::before,
        .dga-home-body .dga-department-title:hover::after,
        .dga-home-body .dga-department-title:focus::after,
        .dga-home-body .dga-department-title.is-active::after,
        .dga-home-body .dga-department-item.is-open .dga-department-title::after {
            opacity: 1;
            transform: scaleX(1);
        }

        .dga-home-body .dga-department-title:hover .dga-department-arrow,
        .dga-home-body .dga-department-title:focus .dga-department-arrow,
        .dga-home-body .dga-department-title.is-active .dga-department-arrow,
        .dga-home-body .dga-department-item.is-open .dga-department-arrow {
            color: var(--dga-theme-accent);
            opacity: 1;
            transform: rotate(180deg);
        }

        .dga-home-body .dga-department-menu {
            left: 8px;
            right: auto;
            top: calc(100% + 7px);
            min-width: 286px;
            padding: 12px 10px;
            border: 1px solid rgba(47, 117, 181, .24);
            border-radius: 14px;
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 20px 46px rgba(6, 39, 68, .18);
            text-align: left;
            transform-origin: left top;
            transform: translateY(10px);
            backdrop-filter: blur(14px);
            overflow: visible;
        }

        .dga-home-body .dga-department-item:hover .dga-department-menu,
        .dga-home-body .dga-department-item:focus-within .dga-department-menu,
        .dga-home-body .dga-department-item.is-open .dga-department-menu {
            transform: translateY(0);
        }

        .dga-home-body .dga-department-menu::before {
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

        .dga-home-body .dga-department-menu a,
        .dga-home-body .dga-department-menu a:nth-child(n) {
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

        .dga-home-body .dga-department-menu a::before {
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

        .dga-home-body .dga-department-menu a:nth-child(1) {
            background: linear-gradient(90deg, rgba(231, 247, 242, .92), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(2) {
            background: linear-gradient(90deg, rgba(230, 246, 255, .92), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(3) {
            background: linear-gradient(90deg, rgba(255, 248, 224, .95), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(4) {
            background: linear-gradient(90deg, rgba(237, 242, 255, .92), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(5) {
            background: linear-gradient(90deg, rgba(239, 250, 231, .92), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(n+6) {
            background: linear-gradient(90deg, rgba(255, 241, 234, .9), rgba(255, 255, 255, .2));
        }

        .dga-home-body .dga-department-menu a:nth-child(n) {
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

        .dga-home-body .dga-department-menu a:nth-child(1) {
            --dga-row-accent: #2f75b5;
            background: linear-gradient(90deg, #ffffff 0%, #eef7ff 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(2) {
            --dga-row-accent: #207985;
            background: linear-gradient(90deg, #ffffff 0%, #edf9f7 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(3) {
            --dga-row-accent: #c18a22;
            background: linear-gradient(90deg, #ffffff 0%, #fff6df 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(4) {
            --dga-row-accent: #2868a6;
            background: linear-gradient(90deg, #ffffff 0%, #f0f5ff 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(5) {
            --dga-row-accent: #258c86;
            background: linear-gradient(90deg, #ffffff 0%, #ecfaf5 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(6) {
            --dga-row-accent: #d0a03d;
            background: linear-gradient(90deg, #ffffff 0%, #fff8e8 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(7) {
            --dga-row-accent: #227b75;
            background: linear-gradient(90deg, #ffffff 0%, #edf9f7 100%);
        }

        .dga-home-body .dga-department-menu a:nth-child(n+8) {
            --dga-row-accent: #2d877d;
            background: linear-gradient(90deg, #ffffff 0%, #eff8ff 100%);
        }

        .dga-home-body .dga-department-menu a:last-child {
            margin-bottom: 0;
        }

        .dga-home-body .dga-department-item:hover .dga-department-menu a,
        .dga-home-body .dga-department-item:focus-within .dga-department-menu a,
        .dga-home-body .dga-department-item.is-open .dga-department-menu a {
            animation: dgaDropdownStepIn .34s ease forwards;
        }

        .dga-home-body .dga-department-menu a:nth-child(1) { animation-delay: .02s; }
        .dga-home-body .dga-department-menu a:nth-child(2) { animation-delay: .06s; }
        .dga-home-body .dga-department-menu a:nth-child(3) { animation-delay: .10s; }
        .dga-home-body .dga-department-menu a:nth-child(4) { animation-delay: .14s; }
        .dga-home-body .dga-department-menu a:nth-child(5) { animation-delay: .18s; }
        .dga-home-body .dga-department-menu a:nth-child(6) { animation-delay: .22s; }
        .dga-home-body .dga-department-menu a:nth-child(7) { animation-delay: .26s; }
        .dga-home-body .dga-department-menu a:nth-child(n+8) { animation-delay: .30s; }

        .dga-home-body .dga-department-menu a:hover,
        .dga-home-body .dga-department-menu a:focus {
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

        .dga-home-body .dga-department-menu a:hover::before,
        .dga-home-body .dga-department-menu a:focus::before {
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

        .dga-home-body .dga-hero-menu {
            overflow: visible;
        }

        .dga-home-body .dga-hero-menu-button,
        .dga-home-body .dga-hero-menu-button:nth-child(n) {
            transform-origin: center;
            transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease, color .18s ease;
        }

        .dga-home-body .dga-hero-menu-button::after {
            width: 100%;
            opacity: 0;
            transition: opacity .16s ease;
        }

        .dga-home-body .dga-hero-menu-button:hover,
        .dga-home-body .dga-hero-menu-button:focus {
            transform: scale(1.085) !important;
        }

        .dga-home-body .dga-hero-menu-button.is-active {
            transform: scale(1.04) !important;
        }

        .dga-home-body .dga-hero-menu-button.is-active:hover,
        .dga-home-body .dga-hero-menu-button.is-active:focus {
            transform: scale(1.095) !important;
        }

        .dga-home-body .dga-hero-menu-button:hover::after,
        .dga-home-body .dga-hero-menu-button:focus::after,
        .dga-home-body .dga-hero-menu-button.is-active::after {
            width: 100%;
            opacity: 1;
        }

        .dga-home-body .dga-bottom-updates {
            width: 100vw;
            height: 390px;
            max-width: none;
            margin: 6px calc(50% - 50vw) 0;
            padding: 6px clamp(18px, 2.8vw, 54px);
            display: grid;
            grid-template-columns: 326px minmax(360px, 1fr) 326px;
            gap: 12px;
            align-items: stretch;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            background:
                linear-gradient(90deg, rgba(232, 247, 252, .95) 0%, #ffffff 22%, #f7fbff 50%, #ffffff 78%, rgba(232, 247, 252, .95) 100%);
            box-shadow: inset 0 1px 0 rgba(47, 117, 181, .14), inset 0 -1px 0 rgba(47, 117, 181, .14), 0 16px 34px rgba(32, 54, 74, .08);
        }

        .dga-home-body .dga-update-photo-frame {
            width: 326px;
            min-height: 20px;
            border-radius: 8px;
        }

        .dga-home-body .dga-bottom-updates-center {
            min-height: 20px;
            border-radius: 8px;
        }

        .dga-home-body .dga-bottom-updates-head {
            min-height: 36px;
            padding: 5px 12px;
        }

        .dga-home-body .dga-bottom-updates-window {
            height: 178px;
            padding: 6px;
        }

        .dga-home-body .dga-bottom-updates-window .dga-updates-track {
            gap: 7px;
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-card {
            min-height: 42px;
            padding: 7px 10px;
        }

        .dga-home-body .dga-bottom-updates-window .dga-update-card p {
            font-size: 11px;
            line-height: 1.32;
        }

        .dga-home-body .dga-home-overview-hub {
            display: grid;
            grid-template-columns: minmax(330px, .9fr) minmax(420px, 1.1fr);
            grid-template-rows: auto;
            gap: 14px;
            min-height: 340px;
            padding: 16px;
            border: 1px solid rgba(47, 117, 181, .16);
            background:
                linear-gradient(120deg, rgba(240, 184, 79, .09) 0 18%, transparent 18% 100%),
                linear-gradient(135deg, rgba(255, 255, 255, .98) 0%, rgba(238, 248, 255, .96) 54%, rgba(243, 251, 248, .96) 100%);
            box-shadow: 0 18px 38px rgba(32, 54, 74, .08);
        }

        .dga-home-body .dga-home-overview-hub .dga-home-intro {
            position: relative;
            z-index: 1;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, #0b3d63 0%, var(--dga-theme-primary) 54%, var(--dga-theme-secondary) 100%);
            color: #ffffff;
            box-shadow: 0 18px 34px rgba(13, 45, 73, .18);
        }

        .dga-home-body .dga-home-overview-hub .dga-home-intro::after {
            content: "";
            position: absolute;
            right: -44px;
            top: 0;
            width: 160px;
            height: 100%;
            border-radius: 0;
            background:
                linear-gradient(120deg, transparent 0 34%, rgba(255, 255, 255, .16) 34% 48%, transparent 48% 100%);
            pointer-events: none;
        }

        .dga-home-body .dga-home-kicker {
            display: inline-flex;
            width: max-content;
            margin-bottom: 10px;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            color: #fff6d7;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .dga-home-body .dga-home-overview-hub .dga-home-intro h2 {
            max-width: 620px;
            margin-bottom: 12px;
            font-size: clamp(28px, 2.4vw, 38px);
            line-height: 1.12;
            color: #ffffff;
        }

        .dga-home-body .dga-home-overview-hub .dga-home-intro p {
            max-width: 720px;
            margin: 0;
            font-size: 14px;
            line-height: 1.55;
            color: rgba(255, 255, 255, .92);
        }

        .dga-home-body .dga-home-overview-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            min-height: 100%;
        }

        .dga-home-body .dga-home-overview-card {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            min-width: 0;
            min-height: 0;
            padding: 18px;
            border: 1px solid rgba(47, 117, 181, .18);
            border-left: 4px solid var(--dga-theme-primary);
            border-radius: 8px;
            background:
                linear-gradient(135deg, #ffffff 0%, #f4fbff 100%);
            box-shadow: 0 14px 28px rgba(32, 54, 74, .08);
        }

        .dga-home-body .dga-home-overview-card.is-mandate {
            border-left-color: var(--dga-theme-secondary);
            background: linear-gradient(135deg, #ffffff 0%, #f1fbf8 100%);
        }

        .dga-home-body .dga-home-overview-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--dga-theme-primary), var(--dga-theme-secondary));
            color: #ffffff;
            font-size: 16px;
            box-shadow: 0 12px 22px rgba(32, 54, 74, .12);
        }

        .dga-home-body .dga-home-overview-card h3 {
            margin: 0 0 9px;
            color: var(--dga-theme-heading);
            font-size: 18px;
            line-height: 1.15;
        }

        .dga-home-body .dga-home-overview-card .dga-hero-list li {
            margin-bottom: 8px;
            padding-left: 19px;
            font-size: 13.2px;
            line-height: 1.48;
        }

        .dga-home-body .dga-home-overview-card .dga-hero-list li:last-child {
            margin-bottom: 0;
        }

        .dga-home-body #dga-panel-wings {
            background:
                linear-gradient(180deg, #ffffff 0%, #f5fbff 100%);
        }

        .dga-home-body #dga-panel-wings > h3 {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 34px;
            margin: 0 0 10px;
            padding: 6px 12px 8px;
            overflow: hidden;
            border: 1px solid rgba(47, 117, 181, .18);
            border-radius: 8px;
            background: linear-gradient(90deg, #ffffff 0%, #eef8ff 100%);
            color: #174f82;
            font-size: 19px;
            line-height: 1.2;
        }

        .dga-home-body #dga-panel-wings > h3::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: auto;
            width: 100%;
            height: 3px;
            border-radius: 0;
            background: linear-gradient(90deg, #1d7e6f, #d09a2d);
            transform: none;
            opacity: 1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network {
            position: relative;
            padding: 12px;
            border: 1px solid rgba(47, 117, 181, .16);
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .98) 0%, rgba(239, 248, 255, .94) 54%, rgba(242, 251, 247, .96) 100%);
            box-shadow: 0 14px 28px rgba(32, 54, 74, .08);
            overflow: hidden;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(47, 117, 181, .055) 1px, transparent 1px),
                linear-gradient(180deg, rgba(72, 169, 154, .055) 1px, transparent 1px);
            background-size: 36px 36px;
            transform: none;
            pointer-events: none;
            z-index: 0;
        }

        /* .dga-home-body #dga-panel-wings .dga-wing-network::after {
            content: "Functional Map";
            position: absolute;
            right: 18px;
            top: 14px;
            z-index: 2;
            padding: 5px 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #174f82, #1d7e6f);
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(29, 126, 111, .14);
            transform: none;
        } */

        .dga-home-body #dga-panel-wings .dga-wing-map-hub {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 0 0 12px;
            padding: 10px 130px 10px 16px;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 8px;
            background: linear-gradient(90deg, #1f7ec5 77%, #166257 90%);
            /* background: linear-gradient(90deg, #0b3d63 0%, #1d7e6f 56%, #b77c18 100%); */
            box-shadow: 0 12px 24px rgba(13, 45, 73, .14);
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub::after {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub span {
            color: rgba(255, 255, 255, .82);
            font-size: 10px;
            letter-spacing: .08em;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub strong {
            color: #ffffff;
            font-size: 20px;
            line-height: 1.1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 0;
            counter-reset: wingMap;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card,
        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(n),
        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
            --wing-accent: #1d7e6f;
            position: relative;
            width: 100%;
            min-height: 112px;
            margin: 0;
            padding: 16px 16px 14px 74px;
            grid-column: auto;
            justify-self: stretch;
            overflow: visible;
            border: 1px solid rgba(29, 126, 111, .18);
            border-left: 4px solid var(--wing-accent);
            border-radius: 8px;
            background: linear-gradient(145deg, #ffffff, color-mix(in srgb, var(--wing-accent) 9%, #ffffff));
            box-shadow: 0 14px 28px rgba(32, 54, 74, .09);
            text-align: left;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even) {
            justify-self: stretch;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(2) {
            --wing-accent: #b77c18;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(3) {
            --wing-accent: #2f75b5;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(4) {
            --wing-accent: #6b5fb8;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
            --wing-accent: #8f5a22;
            margin-bottom: 0;
            grid-column: 1 / -1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::before {
            content: none;
            position: absolute;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::after {
            counter-increment: wingMap;
            content: "0" counter(wingMap);
            position: absolute;
            left: 14px;
            top: 14px;
            z-index: 3;
            width: 38px;
            min-width: 0;
            height: 24px;
            padding: 0;
            border: 1px solid color-mix(in srgb, var(--wing-accent) 26%, #ffffff);
            border-radius: 999px;
            background: color-mix(in srgb, var(--wing-accent) 12%, #ffffff);
            color: var(--wing-accent);
            font-size: 11px;
            font-weight: 900;
            box-shadow: none;
            transform: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::after {
            left: 14px;
            right: auto;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:hover {
            transform: scale(1.012);
            border-color: color-mix(in srgb, var(--wing-accent) 38%, #ffffff);
            box-shadow: 0 22px 44px rgba(32, 54, 74, .14);
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon {
            position: absolute;
            left: 16px;
            right: auto;
            top: 48px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: color-mix(in srgb, var(--wing-accent) 13%, #ffffff);
            color: var(--wing-accent);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9), 0 10px 20px rgba(32, 54, 74, .08);
            transform: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon::after {
            content: none !important;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card h4 {
            margin: 0 0 5px;
            color: #16384f;
            font-size: 14.5px;
            line-height: 1.2;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card p {
            margin: 0;
            color: #4f6474;
            font-size: 11.5px;
            line-height: 1.38;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network {
            padding: 12px;
            background:
                radial-gradient(circle at 7% 12%, rgba(240, 184, 79, .12), transparent 28%),
                radial-gradient(circle at 94% 16%, rgba(72, 169, 154, .14), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #f4fbff 100%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-network::after {
            content: none;
            display: none;
            right: 18px;
            top: 18px;
            background: linear-gradient(90deg, #174f82, #1d7e6f);
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub {
            min-height: 56px;
            justify-content: center;
            margin-bottom: 12px;
            padding: 12px 18px;
            text-align: center;
            /* background: linear-gradient(90deg, #0b3d63 0%, #1d7e6f 55%, #d09a2d 100%); */
            background: linear-gradient(90deg, #1f7ec5 0%, #1f7ec5 72%, #166257 100%);
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub span {
            font-size: 10px;
            letter-spacing: .07em;
        }

        .dga-home-body #dga-panel-wings .dga-wing-map-hub strong {
            font-size: clamp(22px, 1.7vw, 28px);
            line-height: 1.1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            align-items: stretch;
            padding: 8px 0 0;
            counter-reset: wingMap;
        }

        .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before {
            content: "";
            position: absolute;
            left: 5%;
            right: 5%;
            top: 32px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #1d7e6f, #48a99a 42%, #2f75b5 68%, #d09a2d);
            opacity: .72;
            z-index: 0;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card,
        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(n),
        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
            z-index: 1;
            width: 100%;
            min-height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            grid-column: auto;
            justify-self: stretch;
            margin: 0;
            padding: 56px 14px 14px;
            overflow: visible;
            border: 1px solid color-mix(in srgb, var(--wing-accent) 20%, #ffffff);
            border-top: 4px solid var(--wing-accent);
            border-left: 1px solid color-mix(in srgb, var(--wing-accent) 20%, #ffffff);
            border-radius: 8px;
            background:
                linear-gradient(180deg, #ffffff 0%, color-mix(in srgb, var(--wing-accent) 8%, #ffffff) 100%);
            box-shadow: 0 12px 24px rgba(32, 54, 74, .08);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::before {
            content: "";
            position: absolute;
            right: -15px;
            top: 27px;
            z-index: 4;
            width: 20px;
            height: 16px;
            background: var(--wing-accent);
            clip-path: polygon(0 38%, 68% 38%, 68% 0, 100% 50%, 68% 100%, 68% 62%, 0 62%);
            opacity: .86;
            transform: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::before {
            content: "";
            left: auto;
            right: -15px;
            background: var(--wing-accent);
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:last-child::before,
        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5)::before {
            content: none;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card::after {
            counter-increment: wingMap;
            content: "Step " counter(wingMap);
            left: 12px;
            top: 10px;
            width: auto;
            min-width: 52px;
            height: 24px;
            padding: 0 9px;
            border: 1px solid color-mix(in srgb, var(--wing-accent) 30%, #ffffff);
            border-radius: 999px;
            background: color-mix(in srgb, var(--wing-accent) 12%, #ffffff);
            color: var(--wing-accent);
            font-size: 11px;
            line-height: 1;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::after {
            left: 12px;
            right: auto;
        }

        .dga-home-body #dga-panel-wings .dga-wing-icon {
            left: auto;
            right: 12px;
            top: 10px;
            width: 38px;
            height: 38px;
            border-radius: 9px;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card h4 {
            min-height: 34px;
            margin: 0 0 8px;
            font-size: 15px;
            line-height: 1.22;
        }

        .dga-home-body #dga-panel-wings .dga-wing-link-card p {
            font-size: 12.4px;
            line-height: 1.42;
        }

        @media (max-width: 1199px) {
            .dga-home-body .dga-bottom-updates {
                grid-template-columns: 260px minmax(320px, 1fr) 260px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid::before,
            .dga-home-body #dga-panel-wings .dga-wing-link-card::before,
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::before {
                content: none;
            }

            .dga-home-body .dga-update-photo-frame {
                width: 260px;
                min-height: 190px;
            }

            .dga-home-body .dga-bottom-updates-center {
                min-height: 190px;
            }

            .dga-home-body .dga-bottom-updates-window {
                height: 148px;
            }
        }

        @media (max-width: 991px) {
            .dga-home-body .dga-nav {
                width: 100%;
                justify-content: stretch;
            }

            .dga-home-body .dga-nav-link {
                flex: 1 1 0;
                justify-content: center;
                padding: 0 12px;
            }

            .dga-home-body .dga-department-menu {
                min-width: 100%;
                border-radius: 0;
                transform: none;
            }

            .dga-home-body .dga-department-item:hover .dga-department-menu,
            .dga-home-body .dga-department-item:focus-within .dga-department-menu,
            .dga-home-body .dga-department-item.is-open .dga-department-menu {
                transform: none;
            }

            .dga-home-body .dga-bottom-updates {
                width: 100%;
                margin: 10px 0 0;
                padding: 8px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                border-left: 1px solid rgba(47, 117, 181, .16);
                border-right: 1px solid rgba(47, 117, 181, .16);
                border-radius: 8px;
            }

            .dga-home-body .dga-update-photo-frame {
                width: 100%;
                min-height: 96px;
                height: 96px;
            }

            .dga-home-body .dga-bottom-updates-center {
                width: 100%;
                min-height: 120px;
                grid-column: 1 / -1;
                order: 3;
            }

            .dga-home-body .dga-update-photo-frame.is-minister-photo {
                order: 2;
            }

            .dga-home-body .dga-bottom-updates-window {
                height: 120px;
            }

            .dga-home-body .dga-home-overview-grid {
                grid-template-columns: 1fr;
                min-height: 0;
            }

            .dga-home-body .dga-home-overview-hub {
                grid-template-columns: 1fr;
                min-height: auto;
                padding: 12px;
            }

            .dga-home-body .dga-home-overview-hub .dga-home-intro {
                min-height: auto;
                padding: 18px;
            }

            .dga-home-body .dga-home-overview-card {
                padding: 14px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-network {
                padding: 10px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-network::before {
                inset: 0;
                transform: none;
            }

            .dga-home-body #dga-panel-wings .dga-wing-network::after {
                left: auto;
                right: 14px;
                transform: none;
            }

            .dga-home-body #dga-panel-wings .dga-wing-map-hub {
                padding-right: 16px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-connection-grid {
                grid-template-columns: 1fr;
            }

            .dga-home-body #dga-panel-wings .dga-wing-link-card,
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(n),
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(5) {
                width: 100%;
                justify-self: stretch;
                margin: 0;
                min-height: auto;
                padding: 52px 12px 12px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-link-card::before,
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::before {
                content: none;
            }

            .dga-home-body #dga-panel-wings .dga-wing-link-card::after,
            .dga-home-body #dga-panel-wings .dga-wing-link-card:nth-child(even)::after {
                left: 14px;
                right: auto;
                width: 38px;
                height: 24px;
                border-width: 1px;
            }

            .dga-home-body #dga-panel-wings .dga-wing-icon {
                left: auto;
                right: 12px;
                top: 10px;
            }
        }

        @media (max-width: 767px) {
            .dga-home-body .dga-mobile-drawer {
                width: min(86vw, 22rem);
                max-width: 340px;
                top: 52px !important;
                right: 0 !important;
                height: 100dvh;
                z-index: 1050;
            }

            .dga-home-body .dga-mobile-drawer__head {
                position: sticky;
                top: 0;
                z-index: 3;
                display: flex !important;
                flex: 0 0 auto;
            }

            .dga-home-body .dga-mobile-drawer .dga-mobile-nav {
                position: sticky;
                top: 51px;
                z-index: 2;
                display: flex !important;
                flex: 0 0 auto;
                width: 100%;
                gap: 8px;
                padding: 10px 12px;
                background: #ffffff;
                box-shadow: 0 4px 12px rgba(17, 34, 49, .1);
            }

            .dga-home-body .dga-mobile-drawer .dga-mobile-nav .dga-nav-link {
                flex: 1 1 0;
                min-height: 40px;
                padding: 8px 10px;
                white-space: nowrap;
            }

            .dga-home-body .dga-mobile-drawer .dga-department-grid {
                display: flex !important;
                flex-direction: column;
                gap: 8px;
                padding: 12px 0 20px;
            }

            .dga-home-body .dga-department-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 8px;
                padding: 10px 0 12px;
                position: static;
            }

            .dga-home-body .dga-department-item {
                width: 100%;
                min-width: 0;
            }

            .dga-home-body .dga-department-title,
            .dga-home-body .dga-department-title:hover,
            .dga-home-body .dga-department-title:focus {
                width: 100%;
                min-height: 42px;
                justify-content: space-between;
                padding: 10px 14px;
                border: 1px solid rgba(255, 255, 255, .24);
                border-radius: 8px;
                background: rgba(255, 255, 255, .1);
                color: #ffffff;
            }

            .dga-home-body .dga-department-menu {
                position: static;
                display: none !important;
                width: 100%;
                min-width: 0;
                margin-top: 6px;
                padding: 6px;
                border: 1px solid rgba(47, 117, 181, .2);
                border-top: 3px solid var(--dga-theme-accent);
                border-radius: 8px;
                background: #ffffff;
                box-shadow: 0 10px 22px rgba(7, 30, 49, .18);
            }

            .dga-home-body .dga-department-item.is-open .dga-department-menu,
            .dga-home-body .dga-department-item:focus-within .dga-department-menu {
                display: block !important;
            }

            .dga-home-body .dga-department-menu a,
            .dga-home-body .dga-department-menu a:nth-child(n) {
                min-height: 38px;
                margin-bottom: 3px;
                padding: 9px 10px;
                border: 0;
                border-radius: 6px;
                background: #f1f7fb;
                color: #174f82;
                font-size: 12px;
                font-weight: 800;
                text-transform: none;
            }

            .dga-home-body .dga-department-menu a:last-child {
                margin-bottom: 0;
            }

            .dga-home-body .dga-bottom-updates {
                width: 100%;
                height: auto;
                margin: 6px 0 0;
                padding: 6px;
                grid-template-columns: minmax(0, 1fr);
                gap: 6px;
                overflow: visible;
            }

            .dga-home-body .dga-update-photo-frame {
                width: 100%;
                height: 90px;
                min-height: 90px;
                order: 1;
            }

            .dga-home-body .dga-update-photo-frame.is-minister-photo {
                order: 3;
            }

            .dga-home-body .dga-bottom-updates-center {
                width: 100%;
                min-height: 120px;
                grid-column: auto;
                order: 2;
            }

            .dga-home-body .dga-update-frame-image {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        }
	    </style>
	    <link rel="stylesheet" href="{{ asset('site/css/dga-responsive.css') }}?v=20260724q">
</head>

    <body class="dga-home-body" id="dga-page-top">
        @include('dga.partials.header', [
            'departments' => $departments,
            'activeDepartmentSlug' => null,
            'homeHref' => url('/'),
            'homeLabel' => 'CAMS',
            'navLabel' => 'DGA page navigation',
        ])

		    <main>
		        <section class="dga-page-sections-band" aria-label="DGA page sections">
		            <div class="container">
		                <div class="dga-hero-section-grid">
		                    <aside class="dga-hero-menu-card" aria-label="Page sections">
		                        {{-- <span class="dga-menu-kicker">DGA Home</span> --}}
			                        <h3 class="dga-hero-menu-title">DGA Navigator</h3>
		                        {{-- <p class="dga-menu-subtitle">Open each section here without moving down the page.</p> --}}
		                        <div class="dga-hero-menu" role="tablist" aria-label="DGA content menu">
		                            <button type="button" class="dga-hero-menu-button is-active" data-dga-panel-target="home" role="tab" aria-selected="true" aria-controls="dga-panel-home">
		                                <i class="fas fa-home" aria-hidden="true"></i>
		                                <span>Home</span>
		                            </button>
		                            {{-- <button type="button" class="dga-hero-menu-button" data-dga-panel-target="overview" role="tab" aria-selected="false" aria-controls="dga-panel-overview">
			                                <i class="fas fa-university" aria-hidden="true"></i>
		                                <span>Overview</span>
		                            </button> --}}
		                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="flow" role="tab" aria-selected="false" aria-controls="dga-panel-flow">
		                                <i class="fas fa-sitemap" aria-hidden="true"></i>
		                                <span>DGA Flow</span>
		                            </button>
		                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="wings" role="tab" aria-selected="false" aria-controls="dga-panel-wings">
			                                <i class="fas fa-th-list" aria-hidden="true"></i>
		                                <span>Functional Map</span>
		                            </button>
		                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="roadmap" role="tab" aria-selected="false" aria-controls="dga-panel-roadmap">
			                                <i class="fas fa-road" aria-hidden="true"></i>
		                                <span>Road Map</span>
		                            </button>
		                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="process" role="tab" aria-selected="false" aria-controls="dga-panel-process">
		                                <i class="fas fa-cogs" aria-hidden="true"></i>
		                                <span>CAMS Process</span>
		                            </button>
			                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="reforms" role="tab" aria-selected="false" aria-controls="dga-panel-reforms">
			                                <i class="fas fa-sync-alt" aria-hidden="true"></i>
			                                <span>Digital Reforms</span>
			                            </button>
			                            <button type="button" class="dga-hero-menu-button" data-dga-panel-target="modules" role="tab" aria-selected="false" aria-controls="dga-panel-modules">
			                                <i class="fas fa-th-large" aria-hidden="true"></i>
			                                <span>Modules</span>
			                            </button>
		                        </div>
		                    </aside>
		                    <div class="dga-hero-content-shell">
					                        <section class="dga-hero-panel dga-home-panel dga-home-overview-hub is-active" id="dga-panel-home" data-dga-panel="home" role="tabpanel">
					                            <span class="dga-home-visual dga-home-visual-left" aria-hidden="true"></span>
					                            <span class="dga-home-visual dga-home-visual-right" aria-hidden="true"></span>
						                            <div class="dga-home-intro">
						                                {{-- <span class="dga-home-kicker">DGA Home</span> --}}
						                                <h2>{{ $content['hero']['title'] }}</h2>
						                                <p>{{ $content['hero']['subtitle'] }}</p>
						                            </div>
						                            <div class="dga-home-overview-grid">
						                                <article class="dga-home-overview-card">
						                                    <span class="dga-home-overview-icon"><i class="fas fa-university" aria-hidden="true"></i></span>
						                                    <div>
						                                        <h3>Establishment</h3>
						                                        <ul class="dga-hero-list">
						                                            @foreach ($content['overview'] as $point)
						                                                <li>{{ $point }}</li>
						                                            @endforeach
						                                        </ul>
						                                    </div>
						                                </article>
						                                <article class="dga-home-overview-card is-mandate">
						                                    <span class="dga-home-overview-icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
						                                    <div>
						                                        <h3>Mandate</h3>
						                                        <ul class="dga-hero-list">
						                                            @foreach ($content['mandate'] as $point)
						                                                <li>{{ $point }}</li>
						                                            @endforeach
						                                        </ul>
						                                    </div>
						                                </article>
						                            </div>
					                        </section>
		                        <section class="dga-hero-panel dga-overview-panel" id="dga-panel-overview" data-dga-panel="overview" role="tabpanel">
			                            <h3>Why the DGA office was created</h3>
			                            <div class="dga-hero-card-grid">
			                                <article class="dga-hero-info-card">
			                                    <i class="fas fa-university dga-overview-card-icon" aria-hidden="true"></i>
			                                    <h4>Establishment</h4>
			                                    <ul class="dga-hero-list">
			                                        @foreach ($content['overview'] as $point)
			                                            <li>{{ $point }}</li>
			                                        @endforeach
			                                    </ul>
			                                </article>
			                                <article class="dga-hero-info-card">
			                                    <i class="fas fa-clipboard-check dga-overview-card-icon" aria-hidden="true"></i>
			                                    <h4>Mandate</h4>
			                                    <ul class="dga-hero-list">
			                                        @foreach ($content['mandate'] as $point)
			                                            <li>{{ $point }}</li>
			                                        @endforeach
		                                    </ul>
		                                </article>
		                            </div>
		                        </section>
					                        <section class="dga-hero-panel" id="dga-panel-flow" data-dga-panel="flow" role="tabpanel">
					                            <h3>Office structure hub</h3>
					                            <div class="dga-flow-orgmap" aria-label="DGA office structure chart">
					                                <div class="dga-flow-org-caption">
					                                    <span>Structure Dashboard</span>
					                                    {{-- <p>Scan the headquarters setup, core divisions, and open any field directorate directly from this section.</p> --}}
					                                </div>
					                                <div class="dga-flow-org-root">{{ $content['flow_map']['top'] }}</div>
					                                <div class="dga-flow-direction-band" aria-hidden="true">
					                                    <span class="is-left"></span>
					                                    <span class="is-right"></span>
					                                </div>
					                                <div class="dga-flow-org-split">
					                                    <section class="dga-flow-org-column is-hq">
					                                        <div class="dga-flow-org-head">{{ $content['flow_map']['headquarters'] }}</div>
					                                        <p class="dga-flow-column-note">Central administrative coordination, policy support, and functional supervision are grouped here for quick understanding.</p>
					                                        <div class="dga-flow-org-subgrid">
					                                            <div class="dga-flow-subbranch">
					                                                <div class="dga-flow-box is-blue">{{ $content['flow_map']['secretariat'][0] }}</div>
					                                                <div class="dga-flow-box is-green dga-flow-child">{{ $content['flow_map']['secretariat'][1] }}</div>
					                                            </div>
					                                            <div class="dga-flow-subbranch">
					                                                <div class="dga-flow-box is-blue">{{ $content['flow_map']['division_root'] }}</div>
					                                                <div class="dga-flow-division-list">
					                                                    @foreach ($content['flow_map']['divisions'] as $division)
					                                                        <div class="dga-flow-box is-green">{{ $division }}</div>
					                                                    @endforeach
					                                                </div>
					                                            </div>
					                                        </div>
					                                    </section>
					                                    <section class="dga-flow-org-column is-field">
					                                        <div class="dga-flow-org-head">{{ $content['flow_map']['field_title'] }}</div>
					                                        <p class="dga-flow-column-note">Each directorate card opens its dedicated department page with structure, functions, institutions, highlights, and contact details.</p>
					                                        <div class="dga-flow-field-list">
						                                            @foreach ($content['flow_map']['field_directorates'] as $directorate)
						                                                @php($departmentMeta = $departments[$directorate['slug']] ?? null)
						                                                <a class="dga-flow-box is-blue is-link" href="{{ route('dga.department', $directorate['slug']) }}" style="--directorate-accent: {{ $departmentMeta['accent'] ?? '#0e7490' }};">
						                                                    <span class="dga-flow-box-copy">
							                                                        <strong>{{ $directorate['label'] }}</strong>
							                                                    </span>
							                                                </a>
						                                            @endforeach
					                                        </div>
					                                    </section>
					                                </div>
					                            </div>
					                        </section>
			                        <section class="dga-hero-panel" id="dga-panel-wings" data-dga-panel="wings" role="tabpanel">
			                            <h3>Five major wings of the office</h3>
			                            @php($wingIcons = [
			                                'Audit Quality Wing' => 'fas fa-check-circle',
			                                'Audit Reforms Wing' => 'fas fa-sync-alt',
			                                'Performance Audit Wing' => 'fas fa-chart-line',
			                                'Capacity Building and IT Wing' => 'fas fa-laptop',
			                                'Legal Cell Wing' => 'fas fa-balance-scale',
			                            ])
				                            <div class="dga-wing-network">
				                                <div class="dga-wing-map-hub">
				                                    {{-- <span>Functional Map</span> --}}
				                                    <strong>DGA Wings</strong>
				                                </div>
				                                <div class="dga-wing-connection-grid">
				                                    @foreach ($content['wings'] as $wing)
				                                        <article class="dga-wing-link-card">
			                                            <span class="dga-wing-icon">
			                                                <i class="{{ $wingIcons[$wing['name']] ?? 'fas fa-layer-group' }}" aria-hidden="true"></i>
			                                            </span>
			                                            <h4>{{ $wing['name'] }}</h4>
			                                            <p class="mb-0">{{ $wing['detail'] }}</p>
			                                        </article>
			                                    @endforeach
			                                </div>
			                            </div>
			                        </section>
			                        <section class="dga-hero-panel" id="dga-panel-roadmap" data-dga-panel="roadmap" role="tabpanel">
			                            <h3>Key milestones and initiatives</h3>
			                            @php($roadmapIcons = [
			                                'DGA Established' => 'fas fa-university',
			                                'CAMS Approved' => 'fas fa-check-circle',
			                                'Digital Audit Rollout' => 'fas fa-desktop',
			                                'Field Audit Workflow' => 'fas fa-sitemap',
			                                'Faceless Settlement' => 'fas fa-handshake',
			                                'Continuous Improvement' => 'fas fa-chart-line',
			                            ])
			                            <div class="dga-roadmap-network">
			                                <div class="dga-roadmap-connection-list">
			                                    @foreach ($content['roadmap'] as $index => $item)
			                                        <article class="dga-roadmap-link-card">
			                                            <span class="dga-roadmap-step">
			                                                <i class="{{ $roadmapIcons[$item['title']] ?? 'fas fa-circle' }}" aria-hidden="true"></i>
			                                            </span>
			                                            <span class="dga-roadmap-phase">Phase {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
			                                            <h4>{{ $item['title'] }}</h4>
			                                            <p class="mb-0">{{ $item['detail'] }}</p>
			                                        </article>
			                                    @endforeach
			                                </div>
			                            </div>
			                        </section>
		                        <section class="dga-hero-panel" id="dga-panel-process" data-dga-panel="process" role="tabpanel">
		                            <h3>Audit flow from planning to settlement</h3>
			                            <div class="dga-hero-process-grid">
			                                @foreach ($content['process_map'] as $index => $step)
			                                    <article class="dga-hero-mini-step">
			                                        <i class="{{ $step['icon'] }}" aria-hidden="true"></i>
			                                        <span class="dga-hero-process-tag">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
			                                        <h4>{{ $step['title'] }}</h4>
			                                        <p class="mb-0">{{ $step['detail'] }}</p>
			                                    </article>
			                                @endforeach
		                            </div>
		                        </section>
			                        <section class="dga-hero-panel" id="dga-panel-reforms" data-dga-panel="reforms" role="tabpanel">
			                            <div class="dga-panel dga-reforms-panel">
			                                    <div class="dga-section-title">
			                                        <span>Digital Reforms</span>
			                                        <h3>What CAMS changes in audit work</h3>
			                                    </div>
			                                    <ul class="dga-list">
		                                        @foreach ($content['reforms'] as $reform)
			                                            <li>{{ $reform }}</li>
			                                        @endforeach
			                                    </ul>
			                            </div>
			                        </section>
			                        <section class="dga-hero-panel" id="dga-panel-modules" data-dga-panel="modules" role="tabpanel">
			                                    <div class="dga-section-title">
			                                        <span>Modules</span>
			                                        <h3>CAMS modules available</h3>
			                                    </div>
		                                    <div class="dga-module-grid">
		                                        <?php foreach ($content['modules'] as $module): ?>
			                                            <?php
			                                                $moduleName = strtolower($module['name']);
			                                                $moduleIconClass = 'fas fa-layer-group';
			                                                if (str_contains($moduleName, 'auditor')) {
			                                                    $moduleIconClass = 'fas fa-user-plus';
			                                                } elseif (str_contains($moduleName, 'master') || str_contains($moduleName, 'institution')) {
			                                                    $moduleIconClass = 'fas fa-database';
			                                                } elseif (str_contains($moduleName, 'schedule')) {
			                                                    $moduleIconClass = 'fas fa-calendar-alt';
			                                                } elseif (str_contains($moduleName, 'settlement') || str_contains($moduleName, 'compliance')) {
			                                                    $moduleIconClass = 'fas fa-balance-scale';
			                                                } elseif (str_contains($moduleName, 'responsibility') || str_contains($moduleName, 'removal')) {
			                                                    $moduleIconClass = 'fas fa-user-times';
			                                                } elseif (str_contains($moduleName, 'performance') || str_contains($moduleName, 'template')) {
			                                                    $moduleIconClass = 'fas fa-chart-line';
			                                                } elseif (str_contains($moduleName, 'plan')) {
			                                                    $moduleIconClass = 'fas fa-calendar-check';
			                                                } elseif (str_contains($moduleName, 'audit')) {
			                                                    $moduleIconClass = 'fas fa-clipboard-check';
			                                                } elseif (str_contains($moduleName, 'report')) {
			                                                    $moduleIconClass = 'fas fa-file-alt';
			                                                } elseif (str_contains($moduleName, 'monitor') || str_contains($moduleName, 'dashboard')) {
			                                                    $moduleIconClass = 'fas fa-chart-line';
			                                                }
			                                            ?>
		                                            <article class="dga-panel dga-module">
		                                                <span class="dga-module-identity" aria-hidden="true"><i class="{{ $moduleIconClass ?? 'fas fa-layer-group' }}"></i></span>
		                                                <span class="dga-module-label">Module</span>
		                                                <h4>{{ $module['name'] }}</h4>
		                                                <ul>
		                                                    <?php foreach ($module['points'] as $point): ?>
		                                                        <li>{{ $point }}</li>
		                                                    <?php endforeach; ?>
		                                                </ul>
			                                            </article>
			                                        <?php endforeach; ?>
			                                    </div>
			                        </section>
		                        </div>
		                </div>
			                @if (!empty($content['updates']))
			                    <section class="dga-bottom-updates" aria-label="Latest updates">
				                        <div class="dga-update-photo-frame has-image" aria-label="DGA update visual">
				                            <img class="dga-update-frame-image" src="{{ asset('assets/images/cam.jpg') }}" alt="CAMS update visual">
				                            {{-- <span class="dga-update-frame-label">Department Visual</span> --}}
				                        </div>
			                        <div class="dga-bottom-updates-center">
			                            <div class="dga-bottom-updates-head">
			                                <span>Latest</span>
			                                <h3>Updates</h3>
			                            </div>
			                            <div class="dga-updates-window dga-bottom-updates-window">
			                                <div class="dga-updates-track">
				                                    @foreach ($content['updates'] as $update)
				                                        <div class="dga-update-item">
				                                            <article class="dga-update-card">
				                                                <span class="dga-update-date-badge">{{ $update['date'] }}</span>
				                                                <p>{{ $update['text'] }}</p>
			                                            </article>
				                                        </div>
				                                    @endforeach
				                                    @foreach ($content['updates'] as $update)
				                                        <div class="dga-update-item" aria-hidden="true">
				                                            <article class="dga-update-card">
				                                                <span class="dga-update-date-badge">{{ $update['date'] }}</span>
				                                                <p>{{ $update['text'] }}</p>
				                                            </article>
				                                        </div>
				                                    @endforeach
				                                </div>
				                            </div>
				                        </div>
				                        <div class="dga-update-photo-frame has-image is-minister-photo" aria-label="Minister photo">
				                            <img class="dga-update-frame-image" src="{{ asset('assets/images/minister_photo.jpg') }}" alt="Minister photo">
				                        </div>
				                    </section>
							                @endif
								                <section class="dga-home-footer-strip" aria-label="DGA footer">
									                    <div class="dga-home-footer-center">
									                        <span class="dga-home-footer-heading"><i class="fas fa-phone" aria-hidden="true"></i> Website Queries</span>
									                        <span class="dga-home-footer-mail"><i class="fas fa-envelope" aria-hidden="true"></i> cams.dga@tn.gov.in</span>
								                        <p class="dga-home-footer-note">For CAMS portal support, DGA page updates, and technical assistance.</p>
						                    </div>
						                    <div class="dga-home-footer-meta">
						                        <span class="dga-home-footer-heading"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Training and Support</span>
					                        <ul class="dga-footer-support-list">
					                            @foreach ($content['training'] as $training)
					                                <li>{{ $training }}</li>
						                            @endforeach
							                            <li>CAMS helpdesk support is enabled for field audit and APMS issues raised by HODs.</li>
								                        </ul>
									                    </div>
							                </section>
								                <div class="dga-home-footer-credit-row">
								                    Designed and Developed by NIC <strong>&copy; 2026</strong>
								                </div>
						                    <a class="dga-footer-top-link" href="#dga-page-top" data-dga-scroll-top aria-label="Back to top">
					                        <i class="fas fa-arrow-up" aria-hidden="true"></i>
					                    </a>
				            </div>
				        </section>

			        <section class="dga-hero">
		            <div class="container">

			                <div class="dga-hero-layout">
				                    <div class="dga-hero-copy">
				                        <h2>{{ $content['hero']['title'] }}</h2>
				                        <p>{{ $content['hero']['subtitle'] }}</p>

			                    </div>
			                </div>
			            </div>
			        </section>

			        @if (!empty($content['updates']))
			            <section class="dga-updates-section" aria-label="New updates">
			                <div class="container">
			                    <div class="dga-section-title text-center">
			                        <span>New Updates</span>
			                        <h3>Latest DGA and CAMS updates</h3>
		                    </div>
		                    <aside class="dga-updates-panel">
		                        <div class="dga-updates-head">New Updates</div>
		                        <div class="dga-updates-window">
		                            <div class="dga-updates-track">
		                                @foreach ($content['updates'] as $update)
		                                    <div class="dga-update-item">
		                                        <strong>{{ $update['date'] }}</strong>
		                                        <span>{{ $update['text'] }}</span>
		                                    </div>
		                                @endforeach
		                                @foreach ($content['updates'] as $update)
		                                    <div class="dga-update-item" aria-hidden="true">
		                                        <strong>{{ $update['date'] }}</strong>
		                                        <span>{{ $update['text'] }}</span>
		                                    </div>
		                                @endforeach
		                            </div>
		                        </div>
		                    </aside>
		                </div>
		            </section>
		        @endif

		        @include('dga.partials.services')
		        @include('dga.partials.gallery')
		        @include('dga.partials.downloads')
		        @include('dga.partials.contact')

	        <section class="dga-section">

	            <div class="container-fluid px-lg-5">
	                <div class="dga-section-title">
	                    <span>DGA Flow Map</span>
	                    <h3>Office structure hub</h3>
	                </div>
		                <div class="dga-flow-orgmap" aria-label="DGA flow map">
		                    <div class="dga-flow-org-caption">
		                        <span>Structure Dashboard</span>
		                        {{-- <p>Scan the headquarters setup, core divisions, and open any field directorate directly from this section.</p> --}}
		                    </div>
		                    <div class="dga-flow-org-root">{{ $content['flow_map']['top'] }}</div>
		                    <div class="dga-flow-direction-band" aria-hidden="true">
		                        <span class="is-left"></span>
		                        <span class="is-right"></span>
		                    </div>
		                    <div class="dga-flow-org-split">
		                        <section class="dga-flow-org-column is-hq">
		                            <div class="dga-flow-org-head">{{ $content['flow_map']['headquarters'] }}</div>
		                            <p class="dga-flow-column-note">Central administrative coordination, policy support, and functional supervision are grouped here for quick understanding.</p>
		                            <div class="dga-flow-org-subgrid">
		                                <div class="dga-flow-subbranch">
		                                    <div class="dga-flow-box is-blue">{{ $content['flow_map']['secretariat'][0] }}</div>
		                                    <div class="dga-flow-box is-green dga-flow-child">{{ $content['flow_map']['secretariat'][1] }}</div>
		                                </div>
		                                <div class="dga-flow-subbranch">
		                                    <div class="dga-flow-box is-blue">{{ $content['flow_map']['division_root'] }}</div>
		                                    <div class="dga-flow-division-list">
		                                        @foreach ($content['flow_map']['divisions'] as $division)
		                                            <div class="dga-flow-box is-green">{{ $division }}</div>
		                                        @endforeach
		                                    </div>
		                                </div>
		                            </div>
		                        </section>
		                        <section class="dga-flow-org-column is-field">
		                            <div class="dga-flow-org-head">{{ $content['flow_map']['field_title'] }}</div>
		                            <p class="dga-flow-column-note">Each directorate card opens its dedicated department page with structure, functions, institutions, highlights, and contact details.</p>
		                            <div class="dga-flow-field-list">
			                                @foreach ($content['flow_map']['field_directorates'] as $directorate)
			                                    @php($departmentMeta = $departments[$directorate['slug']] ?? null)
			                                    <a class="dga-flow-box is-blue is-link" href="{{ route('dga.department', $directorate['slug']) }}" style="--directorate-accent: {{ $departmentMeta['accent'] ?? '#0e7490' }};">
			                                        <span class="dga-flow-box-copy">
				                                            <strong>{{ $directorate['label'] }}</strong>
				                                        </span>
				                                    </a>
			                                @endforeach
		                            </div>
		                        </section>
		                    </div>
		                </div>
            </div>
        </section>
	        <section class="dga-section pt-0">
	            <div class="container-fluid px-lg-5">
	                <div class="dga-function-roadmap-row">
	                    <div class="dga-function-roadmap-col">
	                        <div class="dga-section-title">
	                            <span>Functional Map</span>
	                            <h3>Five major wings of the office</h3>
	                        </div>
	                        @php($wingIcons = [
	                            'Audit Quality Wing' => 'fas fa-check-circle',
	                            'Audit Reforms Wing' => 'fas fa-sync-alt',
	                            'Performance Audit Wing' => 'fas fa-chart-line',
	                            'Capacity Building and IT Wing' => 'fas fa-laptop',
	                            'Legal Cell Wing' => 'fas fa-balance-scale',
	                        ])
		                        <div class="dga-wing-network">
		                            <div class="dga-wing-map-hub">
		                                <span>Functional Map</span>
		                                <strong>DGA Wings</strong>
		                            </div>
		                            <div class="dga-wing-connection-grid">
		                                @foreach ($content['wings'] as $wing)
		                                    <article class="dga-wing-link-card">
	                                        <span class="dga-wing-icon">
	                                            <i class="{{ $wingIcons[$wing['name']] ?? 'fas fa-layer-group' }}" aria-hidden="true"></i>
	                                        </span>
	                                        <h4>{{ $wing['name'] }}</h4>
	                                        <p class="mb-0">{{ $wing['detail'] }}</p>
	                                    </article>
	                                @endforeach
	                            </div>
	                        </div>
	                    </div>
	                    <div class="dga-function-roadmap-col">
	                        <div class="dga-section-title">
	                            <span>Road Map</span>
	                            <h3>Key milestones and initiatives</h3>
	                        </div>
	                        @php($roadmapIcons = [
	                            'DGA Established' => 'fas fa-university',
	                            'CAMS Approved' => 'fas fa-check-circle',
	                            'Digital Audit Rollout' => 'fas fa-desktop',
	                            'Field Audit Workflow' => 'fas fa-sitemap',
	                            'Faceless Settlement' => 'fas fa-handshake',
	                            'Continuous Improvement' => 'fas fa-chart-line',
	                        ])
		                        <div class="dga-roadmap-network">
		                            <div class="dga-roadmap-connection-list">
		                                @foreach ($content['roadmap'] as $index => $item)
		                                    <article class="dga-roadmap-link-card">
		                                        <span class="dga-roadmap-step">
		                                            <i class="{{ $roadmapIcons[$item['title']] ?? 'fas fa-circle' }}" aria-hidden="true"></i>
		                                        </span>
		                                        <span class="dga-roadmap-phase">Phase {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
		                                        <h4>{{ $item['title'] }}</h4>
		                                        <p class="mb-0">{{ $item['detail'] }}</p>
		                                    </article>
		                                @endforeach
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </section>

        <section class="dga-section bg-white">
	            <div class="container-fluid px-lg-5">
	                <div class="dga-section-title">
	                    <span>CAMS Process Map</span>
	                    <h3>Audit flow from planning to settlement</h3>
	                </div>
	                    <div class="dga-process-flow" aria-label="CAMS audit process map">
	                    @foreach ($content['process_map'] as $index => $step)
	                        <article class="dga-process-step">
	                            <span class="dga-process-icon" aria-hidden="true"><i class="{{ $step['icon'] }}"></i></span>
	                            <span class="dga-process-no">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
	                            <h4>{{ $step['title'] }}</h4>
	                            <p>{{ $step['detail'] }}</p>
	                        </article>
	                    @endforeach
	                </div>
	            </div>
	        </section>

		        <section class="dga-section bg-white dga-modules-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="dga-section-title">
                            <span>Digital Reforms</span>
                            <h3>What CAMS changes in audit work</h3>
                        </div>
                        <ul class="dga-list">
                            @foreach ($content['reforms'] as $reform)
                                <li>{{ $reform }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-lg-7">
                        <div class="dga-section-title">
                            <span>Modules</span>
                            <h3>CAMS modules available</h3>
	                        </div>
	                        <div class="dga-module-grid">
	                            <?php foreach ($content['modules'] as $module): ?>
	                                <?php
	                                    $moduleName = strtolower($module['name']);
	                                    $moduleIconClass = 'fas fa-layer-group';
	                                    if (str_contains($moduleName, 'auditor')) {
	                                        $moduleIconClass = 'fas fa-user-plus';
	                                    } elseif (str_contains($moduleName, 'master') || str_contains($moduleName, 'institution')) {
	                                        $moduleIconClass = 'fas fa-database';
	                                    } elseif (str_contains($moduleName, 'schedule')) {
	                                        $moduleIconClass = 'fas fa-calendar-alt';
	                                    } elseif (str_contains($moduleName, 'settlement') || str_contains($moduleName, 'compliance')) {
	                                        $moduleIconClass = 'fas fa-balance-scale';
	                                    } elseif (str_contains($moduleName, 'responsibility') || str_contains($moduleName, 'removal')) {
	                                        $moduleIconClass = 'fas fa-user-times';
	                                    } elseif (str_contains($moduleName, 'performance') || str_contains($moduleName, 'template')) {
	                                        $moduleIconClass = 'fas fa-chart-line';
	                                    } elseif (str_contains($moduleName, 'plan')) {
	                                        $moduleIconClass = 'fas fa-calendar-check';
	                                    } elseif (str_contains($moduleName, 'audit')) {
	                                        $moduleIconClass = 'fas fa-clipboard-check';
	                                    } elseif (str_contains($moduleName, 'report')) {
	                                        $moduleIconClass = 'fas fa-file-alt';
	                                    } elseif (str_contains($moduleName, 'monitor') || str_contains($moduleName, 'dashboard')) {
	                                        $moduleIconClass = 'fas fa-chart-line';
	                                    }
	                                ?>
	                                <article class="dga-panel dga-module">
	                                    <span class="dga-module-identity" aria-hidden="true"><i class="{{ $moduleIconClass ?? 'fas fa-layer-group' }}"></i></span>
	                                    <span class="dga-module-label">Module</span>
	                                    <h4>{{ $module['name'] }}</h4>
	                                    <ul>
	                                        <?php foreach ($module['points'] as $point): ?>
	                                            <li>{{ $point }}</li>
	                                        <?php endforeach; ?>
	                                    </ul>
	                                </article>
	                            <?php endforeach; ?>
	                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dga-section dga-support-section">
            <div class="container">
                <div class="dga-panel">
                    <div class="row align-items-center">
                        <div class="col-lg-5">
                            <div class="dga-section-title mb-lg-0">
                                <span>Capacity Building</span>
                                <h3>Training and technical support</h3>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <ul class="dga-list">
                                @foreach ($content['training'] as $training)
                                    <li>{{ $training }}</li>
                                @endforeach
                                <li>CAMS helpdesk support is enabled for field audit and APMS issues raised by HODs.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

	        @include('dga.partials.footer', [
	            'footerTitle' => 'Designed and Developed by NIC &copy; 2026',
	            'footerSubtitle' => '',
	            'footerCredit' => 'Designed and Developed by NIC &copy; 2026',
	        ])
			    <script>
				        document.addEventListener('DOMContentLoaded', function () {
				            var topbar = document.querySelector('.dga-topbar');
				            var header = document.querySelector('.dga-header');
				            var menuButtons = document.querySelectorAll('[data-dga-panel-target]');
					            var heroPanels = document.querySelectorAll('[data-dga-panel]');
					            var contentShell = document.querySelector('.dga-hero-content-shell');
					            var footerPanelLinks = document.querySelectorAll('[data-dga-panel-jump]');
						            var topScrollLinks = document.querySelectorAll('[data-dga-scroll-top]');
						            var pageBand = document.querySelector('.dga-page-sections-band');
						            var departmentItems = document.querySelectorAll('.dga-department-item');
						            var mobileDepartmentMedia = window.matchMedia('(max-width: 991px)');
						            var languageButtons = document.querySelectorAll('[data-dga-language]');
						            var themeButtons = document.querySelectorAll('[data-dga-theme]');
						            var fontButtons = document.querySelectorAll('[data-dga-font]');
						            var fontSizes = [14, 16, 18, 20];
						            var defaultFontSize = 16;

						            function getStoredPreference(key, fallback) {
						                try {
						                    return window.localStorage.getItem(key) || fallback;
						                } catch (error) {
						                    return fallback;
						                }
						            }

						            function storePreference(key, value) {
						                try {
						                    window.localStorage.setItem(key, value);
						                } catch (error) {
						                    return;
						                }
						            }

						            function applyLanguage(language) {
						                var selectedLanguage = language === 'ta' ? 'ta' : 'en';
						                document.documentElement.setAttribute('lang', selectedLanguage === 'ta' ? 'ta' : 'en');

						                languageButtons.forEach(function (button) {
						                    var isActive = button.getAttribute('data-dga-language') === selectedLanguage;
						                    button.classList.toggle('is-active', isActive);
						                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
						                });

						                storePreference('dgaLanguage', selectedLanguage);
						            }

						            function applyTheme(theme) {
						                var selectedTheme = ['blue', 'green', 'gold'].indexOf(theme) !== -1 ? theme : 'blue';

						                document.body.classList.remove('dga-theme-green', 'dga-theme-gold');

						                if (selectedTheme === 'green') {
						                    document.body.classList.add('dga-theme-green');
						                } else if (selectedTheme === 'gold') {
						                    document.body.classList.add('dga-theme-gold');
						                }

						                themeButtons.forEach(function (button) {
						                    var isActive = button.getAttribute('data-dga-theme') === selectedTheme;
						                    button.classList.toggle('is-active', isActive);
						                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
						                });

							                storePreference('dgaTheme', selectedTheme);
							                setHomeHeight();
							            }

							            function getFontSizeIndex(size) {
							                var selectedSize = Number(size);
							                var index = fontSizes.indexOf(selectedSize);

							                return index === -1 ? fontSizes.indexOf(defaultFontSize) : index;
							            }

							            function applyFontSize(size) {
							                var selectedSize = fontSizes[getFontSizeIndex(size)];

							                document.documentElement.style.fontSize = selectedSize + 'px';

						                fontButtons.forEach(function (button) {
						                    var action = button.getAttribute('data-dga-font');
						                    var isActive = (selectedSize === defaultFontSize && action === 'reset')
						                        || (selectedSize < defaultFontSize && action === 'decrease')
						                        || (selectedSize > defaultFontSize && action === 'increase');

						                    button.classList.toggle('is-active', isActive);
						                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
						                });

						                storePreference('dgaFontSize', selectedSize);
						                setHomeHeight();
						            }

						            function changeFontSize(action) {
						                var currentSize = getStoredPreference('dgaFontSize', defaultFontSize);
						                var index = getFontSizeIndex(currentSize);

						                if (action === 'increase' && index < fontSizes.length - 1) {
						                    applyFontSize(fontSizes[index + 1]);
						                    return;
						                }

						                if (action === 'decrease' && index > 0) {
						                    applyFontSize(fontSizes[index - 1]);
						                    return;
						                }

						                if (action === 'reset') {
						                    applyFontSize(defaultFontSize);
						                }
						            }

					            function setHomeHeight() {
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
				            }

						            setHomeHeight();
						            window.addEventListener('resize', setHomeHeight);
						            window.addEventListener('load', setHomeHeight);

						            applyLanguage(getStoredPreference('dgaLanguage', 'en'));
						            applyTheme(getStoredPreference('dgaTheme', 'blue'));
						            applyFontSize(getStoredPreference('dgaFontSize', defaultFontSize));

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
						                    changeFontSize(button.getAttribute('data-dga-font'));
						                });
						            });

				            function activatePanel(target) {
				                if (!target) {
				                    return;
				                }

				                menuButtons.forEach(function (item) {
				                    var isActive = item.getAttribute('data-dga-panel-target') === target;
				                    item.classList.toggle('is-active', isActive);
				                    item.setAttribute('aria-selected', isActive ? 'true' : 'false');
				                });

				                heroPanels.forEach(function (panel) {
				                    panel.classList.toggle('is-active', panel.getAttribute('data-dga-panel') === target);
				                });

				                if (contentShell) {
				                    contentShell.classList.add('is-open');
				                }

					                setHomeHeight();
					            }

						            function scrollToDgaTop() {
						                activatePanel('home');

					                window.requestAnimationFrame(function () {
					                    if (pageBand && typeof pageBand.scrollTo === 'function') {
					                        pageBand.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
					                    } else if (pageBand) {
					                        pageBand.scrollTop = 0;
					                    }

					                    if (typeof window.scrollTo === 'function') {
					                        window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
					                    } else {
					                        document.documentElement.scrollTop = 0;
					                        document.body.scrollTop = 0;
					                    }
					                });
					            }

					            menuButtons.forEach(function (button) {
					                button.addEventListener('click', function () {
					                    activatePanel(button.getAttribute('data-dga-panel-target'));
				                });
				            });

					            footerPanelLinks.forEach(function (link) {
					                link.addEventListener('click', function (event) {
					                    var target = link.getAttribute('data-dga-panel-jump');

				                    if (!target) {
				                        return;
				                    }

						                    event.preventDefault();
						                    activatePanel(target);
						                });
						            });

					            topScrollLinks.forEach(function (link) {
					                link.addEventListener('click', function (event) {
					                    event.preventDefault();
					                    scrollToDgaTop();
					                });
					            });

				            function closeDepartmentMenus(exceptItem) {
				                departmentItems.forEach(function (item) {
				                    var isOpen = item === exceptItem;
				                    var trigger = item.querySelector('.dga-department-title');
				                    item.classList.toggle('is-open', isOpen);

				                    if (trigger) {
				                        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
				                    }
				                });
				            }

				            departmentItems.forEach(function (item) {
				                var trigger = item.querySelector('.dga-department-title');

				                if (!trigger) {
				                    return;
				                }

					                trigger.addEventListener('click', function (event) {
					                    if (event.target.closest('.dga-department-arrow')) {
					                        event.preventDefault();
					                        closeDepartmentMenus(item.classList.contains('is-open') ? null : item);
					                        return;
					                    }

					                    if (!mobileDepartmentMedia.matches) {
					                        return;
					                    }

				                    if (!item.classList.contains('is-open')) {
				                        event.preventDefault();
				                        closeDepartmentMenus(item);
				                    }
				                });
				            });

				            document.addEventListener('click', function (event) {
				                if (!mobileDepartmentMedia.matches) {
				                    return;
				                }

				                if (!event.target.closest('.dga-department-item')) {
				                    closeDepartmentMenus(null);
				                }
				            });

				            window.addEventListener('resize', function () {
				                if (!mobileDepartmentMedia.matches) {
				                    closeDepartmentMenus(null);
				                }
				            });

			            var flowMap = document.querySelector('.dga-flow-map');

		            if (!flowMap) {
	                return;
	            }

	            if (!('IntersectionObserver' in window)) {
	                flowMap.classList.add('is-visible');
	                return;
	            }

	            var observer = new IntersectionObserver(function (entries) {
	                entries.forEach(function (entry) {
	                    if (entry.isIntersecting) {
	                        entry.target.classList.add('is-visible');
	                    } else {
	                        entry.target.classList.remove('is-visible');
	                    }
	                });
	            }, {
	                threshold: 0.25
	            });

	            observer.observe(flowMap);
	        });
	    </script>
	    <script src="{{ asset('site/js/dga-responsive.js') }}?v=20260724e"></script>
	</body>

</html>
