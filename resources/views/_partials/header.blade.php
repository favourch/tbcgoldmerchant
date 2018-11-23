<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TBC Gold Merchant</title>
<link rel="icon" href="{{ asset('favicon.ico') }}" />

<link rel="stylesheet" type="text/css" href="{{ asset('css/semantic.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/family-tree.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@3.5.2/animate.min.css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Shrikhand" rel="stylesheet">

{{-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet"> --}}
<style>
	p, li, a, h1, h2, h3, h4, h5, small, label, input, select, button {
		font-family: 'Montserrat', sans-serif;
	}

  .hidden.menu {
    display: none;
  }
  .masthead.segment {
    min-height: 650px;
    padding: 1em 0em;
  }
  .masthead .logo.item img {
    margin-right: 1em;
  }
  .masthead .ui.menu .ui.button {
    margin-left: 0.5em;
  }
  .masthead h1.ui.header {
    margin-top: 2.5em;
    margin-bottom: 0.5em;
    font-size: 4.8em;
    font-weight: normal;
  }
  .masthead h2 {
    font-size: 1.7em;
    font-weight: normal;
    margin-left: -400px;
  }
  .ui.vertical.stripe {
    padding: 5em 0em;
  }
  .ui.vertical.stripe h3 {
    font-size: 2em;
  }
  .ui.vertical.stripe .button + h3,
  .ui.vertical.stripe p + h3 {
    margin-top: 3em;
  }
  .ui.vertical.stripe .floated.image {
    clear: both;
  }
  .ui.vertical.stripe p {
    font-size: 1.2em;
  }
  .ui.vertical.stripe .horizontal.divider {
    margin: 3em 0em;
  }
  .quote.stripe.segment {
    padding: 0em;
  }
  .quote.stripe.segment .grid .column {
    padding-top: 5em;
    padding-bottom: 5em;
  }
  .footer.segment {
    padding: 5em 0em;
  }
  .secondary.pointing.menu .toc.item {
    display: none;
  }
  @media only screen and (max-width: 700px) {
    .ui.fixed.menu {
      display: none !important;
    }
    .secondary.pointing.menu .item,
    .secondary.pointing.menu .menu {
      display: none;
    }
    .secondary.pointing.menu .toc.item {
      display: block;
    }
    .masthead.segment {
      min-height: 350px;
    }
    .masthead h1.ui.header {
      font-size: 2em;
      margin-top: 1.5em;
    }
    .masthead h2 {
      margin-top: 0.5em;
      font-size: 1.5em;
    }
  }

  .ui.secondary.pointing.menu .item:hover{
    background-color: transparent;
    -webkit-box-shadow: none;
    box-shadow: none;
    border-color: #1B1C1D;
    font-weight: bold;
    color: rgba(0, 0, 0, 0.95);
  }
</style>