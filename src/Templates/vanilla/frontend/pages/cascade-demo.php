<?php
declare(strict_types = 1);

/**
 * Cascade Demo Page - Includes cascade layout
 *
 * @var string $pageTitle
 */
$pageTitle ??= 'Cascade Demo - Nativa CMS';

// Include cascade layout header
include __DIR__ . '/../layouts/cascade-header.php';
?>

<div class="pad">
  <h1>Cascade Framework Demo</h1>
  <p class="lead">This page demonstrates Cascade Framework integration with Nativa CMS</p>
  
  <hr>
  
  <!-- Grid Demo -->
  <h2>Grid System</h2>
  <div class="col width-fill">
    <div class="col width-fit">
      <div class="cell pad">
        <strong>width-fit</strong><br>
        Adapts to content width
      </div>
    </div>
    <div class="col width-fill">
      <div class="cell pad">
        <strong>width-fill</strong><br>
        Fills remaining space
      </div>
    </div>
  </div>
  
  <hr>
  
  <!-- Typography Demo -->
  <h2>Typography</h2>
  <div class="typography">
    <h1>Heading 1</h1>
    <h2>Heading 2</h2>
    <h3>Heading 3</h3>
    <p>This is a paragraph with <a href="#">a link</a> and <strong>bold text</strong>.</p>
    <blockquote>
      <p>"Cascade Framework makes building websites easier than ever."</p>
      <small>- John Legers</small>
    </blockquote>
  </div>
  
  <hr>
  
  <!-- Components Demo -->
  <h2>Components</h2>
  
  <!-- Buttons -->
  <h3>Buttons</h3>
  <div class="pad">
    <button class="btn">Default Button</button>
    <button class="btn primary">Primary</button>
    <button class="btn info">Info</button>
    <button class="btn success">Success</button>
    <button class="btn warning">Warning</button>
    <button class="btn danger">Danger</button>
  </div>
  
  <!-- Navigation Blocks -->
  <h3>Navigation Blocks</h3>
  <ul class="nav blocks">
    <li><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
  
  <!-- Table Demo -->
  <h3>Tables</h3>
  <table class="files">
    <thead>
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Size</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>core.css</td>
        <td>CSS File</td>
        <td>10 KB</td>
      </tr>
      <tr>
        <td>icons.css</td>
        <td>CSS File</td>
        <td>20 KB</td>
      </tr>
      <tr>
        <td>helpers.css</td>
        <td>CSS File</td>
        <td>5 KB</td>
      </tr>
    </tbody>
  </table>
  
  <hr>
  
  <!-- Icons Demo -->
  <h2>Icons (Font Awesome)</h2>
  <div class="pad">
    <span class="icon icon-32 icon-home"></span>
    <span class="icon icon-32 icon-user"></span>
    <span class="icon icon-32 icon-search"></span>
    <span class="icon icon-32 icon-star"></span>
    <span class="icon icon-32 icon-heart"></span>
    <span class="icon icon-32 icon-cog"></span>
  </div>
  
  <hr>
  
  <!-- Helper Classes Demo -->
  <h2>Helper Classes</h2>
  <div class="pad">
    <p class="muted">This text is muted</p>
    <p class="warning">This is a warning</p>
    <p class="error">This is an error</p>
    <p class="success">This is success</p>
    <p class="info">This is info</p>
  </div>
  
</div>

<?php
// Include cascade layout footer
include __DIR__ . '/../layouts/cascade-footer.php';
