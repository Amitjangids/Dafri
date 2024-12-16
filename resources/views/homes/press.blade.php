@extends('layouts.home_new')
@section('content')

<section class="same-section blog-details-section job-details-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-2">
          <div class="header-back-btn">
            <a href="{{HTTP_PATH}}"><img src="{{PUBLIC_PATH}}/assets/fonts/images/backicon.svg" alt="image"> Back</a>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="blog-details-parent">
            <h2>Design Manager</h2>
            <p>Lead the design team in creating impactful, user-centered solutions for our digital products. Drive design strategy and ensure a seamless user experience.</p>
            <ul>
              <li>
                <label>Location:</label>
                <p>Remote / Office</p>
              </li>
              <li>
                <label>Type</label>
                <p>Full Time</p>
              </li>
              <li>
                <label>Salary</label>
                <p>$85,000 - $100,000</p>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-2"></div>
      </div>
      <div class="row job-details-inner">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
          <div class="center-content">
            <h3>About the Role</h3>
            <p>As the Design Manager at Dafri Premier™, you will lead our talented design team in creating impactful, user-centered solutions for our digital products. You will be responsible for driving the design strategy, mentoring designers, and ensuring a seamless, cohesive user experience across all touchpoints. Your work will directly shape the future of our platform and contribute to enhancing user satisfaction and engagement.</p>
          </div>
        </div>
        <div class="col-lg-2"></div>
      </div>
      <div class="row job-details-inner">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
          <div class="center-content">
            <h3>Key Responsibilities</h3>
            <div class="row">
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Leadership & Strategy</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Define and drive the design vision in alignment with company goals and product strategy. Lead the design team by setting clear objectives and providing constructive feedback.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Project Management</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Oversee multiple design projects from conception to completion, ensuring timelines and quality standards are met.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Collaboration</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Work closely with cross-functional teams, including Product, Engineering, Marketing, and Customer Support, to understand user needs and deliver design solutions that address those needs.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>User Experience <br>Optimization</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Work closely with cross-functional teams, including Product, Engineering, Marketing, and Customer Support, to understand user needs and deliver design solutions that address those needs.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-2"></div>
      </div>
      <div class="row job-details-inner">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
          <div class="center-content">
            <h3>What We Offer</h3>
            <div class="row">
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Competitive Salary</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>We offer a competitive salary with room for growth.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Flexible Work Environment</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Choose to work remotely or from our office, with flexible hours.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Professional Development</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Opportunities for ongoing learning and professional growth through training, conferences, and workshops.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="center-content-mini-heading">
                  <span></span>
                  <h6>Healthcare Benefits</h6>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="center-mini-content">
                  <p>Comprehensive health insurance and wellness programs.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-2"></div>
      </div>
    </div>
  </section>

  <section class="same-section dark-bg-color job-cta-section">
    <div class="container">
      <div class="job-desc-cta-parent">
        <div class="same-heading text-center">
            <h2>Application Process</h2>
            <p>To apply, please send your CV and portfolio to careers.dafripremier@hr with "Design Manager Application" in the subject line. In your application, include a short motivational letter explaining why you are the perfect fit for this role.</p>
            <div class="btnsr section-btn-spacing">
              <ul><li><a href="javascript:" class="btn btn-primaryx">Apply now</a></li></ul>
            </div>
          </div>
      </div>
    </div>
  </section>
@endsection