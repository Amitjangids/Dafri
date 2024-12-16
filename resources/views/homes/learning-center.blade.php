@extends('layouts.home_new')
@section('content')        
     
<section class="same-section learning-section">
        <div class="container">
          <div class="same-page-heading">
            <h2>Explore Resources for <br>Better Understanding.<sup>tM</sup></h2>
          </div>
          <div class="learning-main-wrapper">
            <div class="banner-content">
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search" aria-label="Recipient's username" aria-describedby="button-addon2">
                <button class="btn btn-primaryx" type="button" id="button-addon2">Search</button>
              </div>
            </div>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">General</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Security & privacy </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Moving money </button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab01" data-bs-toggle="tab" data-bs-target="#contact01" type="button" role="tab" aria-controls="contact01" aria-selected="false">Debit card </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab02" data-bs-toggle="tab" data-bs-target="#contact02" type="button" role="tab" aria-controls="contact02" aria-selected="false">Savings account </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab03" data-bs-toggle="tab" data-bs-target="#contact03" type="button" role="tab" aria-controls="contact03" aria-selected="false">User accounts </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab04" data-bs-toggle="tab" data-bs-target="#contact04" type="button" role="tab" aria-controls="contact04" aria-selected="false">Pricing</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact01" role="tabpanel" aria-labelledby="contact-tab01">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact02" role="tabpanel" aria-labelledby="contact-tab02">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact03" role="tabpanel" aria-labelledby="contact-tab03">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact04" role="tabpanel" aria-labelledby="contact-tab04">
                <div class="learning-accodion-parent">
                  <div class="accordion" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                          What is DarfiBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                          What do you do for security?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          What types of accounts are available?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefour" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                          How secure is my personal information with DafriBank?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfour">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                     <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingfive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsefive" aria-expanded="false" aria-controls="panelsStayOpen-collapsefive">
                          Can I transfer funds internationally?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsefive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingfive">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="panelsStayOpen-headingsix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsesix" aria-expanded="false" aria-controls="panelsStayOpen-collapsesix">
                          Are there fees associated with transactions?
                        </button>
                      </h2>
                      <div id="panelsStayOpen-collapsesix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingsix">
                        <div class="accordion-body">
                          <p>DafriBank offers a variety of accounts to suit your needs, including personal savings accounts, business accounts, and investment accounts. Each account has unique features to help you manage your finances effectively.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
@endsection