<?php
/**
 * @package     omeka
 * @subpackage  solr-search
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */
?>

<?php queue_css_file('results'); ?>
<?php echo head(array('title' => __('Solr Search')));?>
<script>
  jQuery( document ).ready(function() {
    jQuery('.facet-label').click(function(){

      jQuery(this).next(".facet-list").toggle();

      if(jQuery(this).hasClass("active")){
        jQuery(this).removeClass("active");
      }else{
        jQuery(this).addClass("active");
      }

      if(jQuery(this).next(".facet-list").hasClass("active")){
        jQuery(this).next(".facet-list").removeClass("active");
      }else{
        jQuery(this).next(".facet-list").addClass("active");
      }

    });
  });
</script>
<?php
    $title = 'Search the Collection';
    if(isset($_GET['facet'])):
        if(strpos($_GET['facet'],'News') !== false || strpos($_GET['facet'],'Event')):
            $title = "News & events";
        endif;
    endif;
?>

<h1><?php echo __($title); ?></h1>

<!-- Search form. -->
<div id="solr-form-div">
  <form id="solr-search-form">
    <input type="submit" value="Search" />
    <span class="float-wrap">
      <input type="text" title="<?php echo __('Search keywords') ?>" name="q" value="<?php
        echo array_key_exists('q', $_GET) ? htmlspecialchars(strip_tags($_GET['q'])) : '';
      ?>" />
    </span>
    <a class="search-help" href="<?php echo url('search-help'); ?>">&RightTeeArrow; Search tips</a>
  </form>
</div>

<!-- Applied facets. -->
<div id="solr-applied-facets">
  <ul>
    <!-- Get the applied facets. -->
    <?php foreach (SolrSearch_Helpers_Facet::parseFacets() as $f): ?>
      <li>
        <!-- Facet label. -->
        <?php $label = SolrSearch_Helpers_Facet::keyToLabel($f[0]); ?>
        <span class="applied-facet-label"><?php echo $label; ?></span> :
        <span class="applied-facet-value"><?php echo $f[1]; ?></span>
        <!-- Remove link. -->
        <?php $url = SolrSearch_Helpers_Facet::removeFacet($f[0], $f[1]); ?>
        <a href="<?php echo $url; ?>">x</a>
      </li>
    <?php endforeach; ?>

    <?php if(SolrSearch_Helpers_Facet::parseFacets()):?>
      <li><a href="<?php echo url('solr-search?q='.$_GET['q']) ?>">Remove all filters</a></li>
    <?php endif;?>
  </ul>
</div>
<div><?php echo pagination_links(); ?></div>
<!-- Facets. -->
<div id="solr-facets">
  <h2><?php echo __('Limit your search'); ?></h2>
  <?php $i=0;?>
  <?php foreach ($results->facet_counts->facet_fields as $name => $facets): ?>
    <?php $i++;?>
    <!-- Does the facet have any hits? -->
    <?php if (count(get_object_vars($facets))): ?>

      <!-- Facet label. -->
      <?php $label = SolrSearch_Helpers_Facet::keyToLabel($name); ?>
      <h5 id="label-<?php echo $i;?>" class="facet-label"><?php echo $label; ?></h5>

      <ul id="list-<?php echo $i;?>" class="facet-list">
        <!-- Facets. -->
        <?php foreach ($facets as $value => $count): ?>
          <li class="<?php echo $value; ?>">

            <!-- Facet URL. -->
            <?php $url = SolrSearch_Helpers_Facet::addFacet($name, $value); ?>

            <!-- Facet link. -->
            <a href="<?php echo $url; ?>" class="facet-value">
              <?php echo $value; ?>
            </a>

            <!-- Facet count. -->
            (<span class="facet-count"><?php echo $count; ?></span>)

          </li>
        <?php endforeach; ?>
      </ul>

    <?php endif; ?>

  <?php endforeach; ?>
</div>


<!-- Results. -->
<div id="solr-results">

  <!-- Number found. -->
  <h2 id="num-found">
    <?php echo $results->response->numFound; ?> results
  </h2>

  <?php foreach ($results->response->docs as $doc):?>
    <?php $item = get_record_by_id('item',preg_replace ( '/[^0-9]/', '', $doc->__get('id')));?>
    <?php set_current_record('item',$item); ?>
    <!-- Document. -->
    <div class="result">
      <!-- Header. -->
      <div class="result-header">

        <!-- Record URL. -->
        <?php $url = SolrSearch_Helpers_View::getDocumentUrl($doc); ?>

        <!-- Title. -->
        <a href="<?php echo $url; ?>" class="result-title">
            <?php
                $title = metadata('item', array('Dublin Core', 'Title'));

                if (empty($title)) {
                    $title = '<i>' . __('Untitled') . '</i>';
                }
                echo $title;

            ?>
        </a>

      </div>

      <!-- Image. -->

        <!-- Document. -->
        <?php if (item_image('thumbnail')): ?>
            <div class="item-img">
                <a href="<?php echo record_url($item) ?>"><?php echo item_image('thumbnail'); ?></a>
            </div>
        <?php endif; ?>

        <div class='item-metadata'>

            <table class="sho-table browse-table">
            <?php if ($text = metadata('item', array('Dublin Core', 'Source'),array('delimiter'=>', '))): ?>
            <tr><td>
                <b>Source</b>
            </td><td>
                <?php echo $text; ?>
            </td></tr>
            <?php endif; ?>

            <?php if ($text = metadata('item', array('Item Type Metadata', 'Call number'),array('delimiter'=>', '))): ?>
            <tr><td>
                <b>Call number</b>
            </td><td>
                <?php echo $text; ?>
            </td></tr>
            <?php endif; ?>

            <?php if ($text = metadata('item', array('Dublin Core', 'Date'),array('delimiter'=>', '))): ?>
            <tr><td>
                <b>Date</b>
            </td><td>
                <?php echo $text; ?>
            </td></tr>
            <?php endif; ?>


            </table>
        </div>
    </div>

  <?php endforeach; ?>
</div>


<?php echo pagination_links(); ?>
<?php echo foot();?>
