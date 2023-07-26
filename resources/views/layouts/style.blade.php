{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css"/> --}}

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet"> --}}
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

<style type="text/css">
@import url('http://fonts.cdnfonts.com/css/sf-pro-display');

body{
	font-family: 'SF Pro Display', sans-serif;
}

</style>

<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
/* Auto layout */
.beambtnlayout
{
width:336px;
height:50px;
top:-105px;
left:356px;
background: #08154D;
border-color: transparent;
border-radius: 6px;
padding:17px, 80px, 17px, 80px;
gap:8px;
/* background-image: {{ asset('images/beamprimary.png') }}; */
background:url(/images/beamprimary.png) no-repeat;
/* background:url(/images/beamchkout.png) no-repeat; */
}

.text-right_wraps {
    float: right;
    position: relative;
    top: 11px;
}



a.install_button_wraps {
    padding: 13px 25px;
    font-size: 16px;
}

hr.wrap_line {
    background: #80808047;
    height: 3px;
    margin: 13px 0px;
}

.row.wraps_first-div {
    margin-top: 20px;
    margin-bottom: 20px;
}

input.w3-check.wrap_checkbox {
    height: 16px;
    width: 16px;
    margin-right: 8px;
}

span.wrap_checkobx_text {
    font-size: 17px;
    position: relative;
    top: 4px;
}

.wrap_chkbox {
    border-bottom: 2px solid #80808040;
    padding-bottom: 15px;
    margin-bottom: 10px;
}

.wrap_chkbox:last-child {
    border-bottom: 0px;
    padding-bottom: 0px;
    margin-bottom: 10px;
}

.wrap_text-heading {
    border-bottom: 2px solid #80808040;
    padding-bottom: 15px;
    margin-bottom: 10px;
    font-size: 18px;
}

.flex_wraps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

p.appear_wrap_text {
    font-size: 17px;
    margin-bottom: 0px;
}



</style>

<!-------------- NEW BTN CSS START ----------------->
<style>

@media only screen and (max-width: 360px)  {
button#beamcheckoutbutton{
    width: 357px;
    height: 49px;
    border-style: none;
    border-radius: 10px;
    background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
    background-size: cover;
    cursor: pointer !important;
    margin: 0px auto;
    margin-top: 10px !important;
    text-align: center;
}
}
@media only screen and (max-width: 676px) {
    button#beamcheckoutbutton{
    width: 311px;
        height: 49px;
        border-style: none;
        border-radius: 10px;
        background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
        background-size: cover;
        cursor: pointer !important;
        margin: 0px auto;
        margin-top: 10px !important;
        text-align: center;
    }
    }
    button#beamcheckoutbutton{
    width: 439px;
    height: 49px;
    border-style: none;
    border-radius: 10px;
    background: url(https://phpstack-102119-3041881.cloudwaysapps.com/storage/img/Primarys.svg) no-repeat;
    background-size: cover;
    cursor: pointer !important;
    margin: 0px auto;
    margin-top: 10px !important;
    text-align: center;
}

</style>
<!-------------- NEW BTN CSS END ----------------->
