<?php
//
// Musicbrainz API integration
//
add_action('add_meta_boxes', 'add_musicbrainz_scanner_metabox');

function add_musicbrainz_scanner_metabox()
{
  add_meta_box(
    'musicbrainz-scanner',
    __('MusicBrainz Tag Scanner', 'dma_engine'),
    'musicbrainz_scanner_metabox_html',
    'dma_alba',
    'side',
    'default'
  );
}

function musicbrainz_scanner_metabox_html($post)
{
  // Get current artist and genre data
  $artist_terms = get_the_terms($post->ID, 'artiest');
  $genre_terms = get_the_terms($post->ID, 'genre');
  $style_terms = get_the_terms($post->ID, 'style');
  $mood_terms = get_the_terms($post->ID, 'mood');

  $artist_name = $artist_terms && !is_wp_error($artist_terms) ? $artist_terms[0]->name : '';
  $album_title = $post->post_title;

?>
  <div id="musicbrainz-scanner-content">
    <div class="mb-scanner-info">
      <p><strong>Artist:</strong> <span id="mb-current-artist"><?php echo esc_html($artist_name); ?></span></p>
      <p><strong>Album:</strong> <span id="mb-current-album"><?php echo esc_html($album_title); ?></span></p>
    </div>

    <div class="mb-scanner-controls">
      <button type="button" id="scan-musicbrainz-tags" class="button button-primary" <?php echo empty($artist_name) || empty($album_title) ? 'disabled' : ''; ?>>
        <span class="button-text">Scan for Tags</span>
        <span class="spinner" style="display: none; float: none; margin: 0 0 0 8px;"></span>
      </button>
    </div>

    <div id="mb-scan-results" style="display: none; margin-top: 15px;">
      <h4>Found Tags:</h4>
      <div id="mb-results-content"></div>
      <div class="mb-apply-controls" style="margin-top: 10px;">
        <button type="button" id="apply-selected-tags" class="button button-secondary">Apply Selected Tags</button>
        <button type="button" id="cancel-scan" class="button">Cancel</button>
      </div>
    </div>

    <div id="mb-scan-error" style="display: none; margin-top: 15px; color: #d63638;">
      <p id="mb-error-message"></p>
    </div>
  </div>

  <style>
    .mb-tag-suggestion {
      margin: 8px 0;
      padding: 8px;
      background: #f9f9f9;
      border-left: 3px solid #007cba;
    }

    .mb-tag-suggestion label {
      display: block;
      font-weight: bold;
      margin-bottom: 4px;
    }

    .mb-tag-suggestion input[type="checkbox"] {
      margin-right: 6px;
    }

    .mb-current-tags {
      font-style: italic;
      color: #666;
      font-size: 12px;
    }

    .mb-new-tags {
      color: #007cba;
    }
  </style>

  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('#scan-musicbrainz-tags').on('click', function() {
        var button = $(this);
        var spinner = button.find('.spinner');
        var buttonText = button.find('.button-text');

        // Reset previous results
        $('#mb-scan-results, #mb-scan-error').hide();

        // Show loading state
        button.prop('disabled', true);
        spinner.show();
        buttonText.text('Scanning...');

        var data = {
          action: 'scan_musicbrainz_tags',
          post_id: <?php echo $post->ID; ?>,
          artist: $('#mb-current-artist').text(),
          album: $('#mb-current-album').text(),
          nonce: '<?php echo wp_create_nonce('musicbrainz_scan_' . $post->ID); ?>'
        };

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: data,
          success: function(response) {
            button.prop('disabled', false);
            spinner.hide();
            buttonText.text('Scan for Tags');

            if (response.success) {
              displayScanResults(response.data);
            } else {
              showError(response.data.message || 'Unknown error occurred');
            }
          },
          error: function() {
            button.prop('disabled', false);
            spinner.hide();
            buttonText.text('Scan for Tags');
            showError('Network error occurred. Please try again.');
          }
        });
      });

      $('#apply-selected-tags').on('click', function() {
        var selectedTags = {
          genres: [],
          styles: [],
          moods: [],
          labels: [],
          jaren: [],
          cover: []
        };
        
        // Collect selected tags
        $('#mb-results-content input:checked').each(function() {
          var checkbox = $(this);
          var tagType = checkbox.data('type');
          var tagName = checkbox.data('tag');

          if (selectedTags[tagType]) {
            selectedTags[tagType].push(tagName);
          }
        });

        // Apply tags via AJAX
        var data = {
          action: 'apply_musicbrainz_tags',
          post_id: <?php echo $post->ID; ?>,
          tags: selectedTags,
          nonce: '<?php echo wp_create_nonce('musicbrainz_apply_' . $post->ID); ?>'
        };

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: data,
          success: function(response) {
            if (response.success) {
              location.reload(); // Refresh to show updated taxonomies
            } else {
              showError(response.data.message || 'Error applying tags');
            }
          },
          error: function() {
            showError('Network error occurred while applying tags.');
          }
        });
      });

      $('#cancel-scan').on('click', function() {
        $('#mb-scan-results, #mb-scan-error').hide();
      });

      function displayScanResults(data) {
        var html = '';

        if (data.genres && data.genres.length > 0) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Genres:</label>';
          data.genres.forEach(function(genre) {
            html += '<div><input type="checkbox" data-type="genres" data-tag="' + escapeHtml(genre) + '" checked> <span class="mb-new-tags">' + escapeHtml(genre) + '</span></div>';
          });
          html += '</div>';
        }

        if (data.styles && data.styles.length > 0) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Styles:</label>';
          data.styles.forEach(function(style) {
            html += '<div><input type="checkbox" data-type="styles" data-tag="' + escapeHtml(style) + '" checked> <span class="mb-new-tags">' + escapeHtml(style) + '</span></div>';
          });
          html += '</div>';
        }

        if (data.moods && data.moods.length > 0) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Moods:</label>';
          data.moods.forEach(function(mood) {
            html += '<div><input type="checkbox" data-type="moods" data-tag="' + escapeHtml(mood) + '" checked> <span class="mb-new-tags">' + escapeHtml(mood) + '</span></div>';
          });
          html += '</div>';
        }

        if (data.labels && data.labels.length > 0) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Labels:</label>';
          data.labels.forEach(function(label) {
            html += '<div><input type="checkbox" data-type="labels" data-tag="' + escapeHtml(label) + '" checked> <span class="mb-new-tags">' + escapeHtml(label) + '</span></div>';
          });
          html += '</div>';
        }

        if (data.year) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Year:</label>';
          html += '<div><input type="checkbox" data-type="jaren" data-tag="' + escapeHtml(data.year) + '" checked> <span class="mb-new-tags">' + escapeHtml(data.year) + '</span></div>';
          html += '</div>';
        }

        if (data.cover_url) {
          html += '<div class="mb-tag-suggestion">';
          html += '<label>Cover Art:</label>';
          html += '<div><input type="checkbox" data-type="cover" data-tag="' + escapeHtml(data.cover_url) + '" checked> <span class="mb-new-tags">Set as featured image</span></div>';
          html += '</div>';
        }

        if (html === '') {
          html = '<p>No genre, style, or mood tags found for this album.</p>';
        }

        $('#mb-results-content').html(html);
        $('#mb-scan-results').show();
      }

      function showError(message) {
        $('#mb-error-message').text(message);
        $('#mb-scan-error').show();
      }

      function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }
    });
  </script>
<?php
}

// AJAX handler for scanning MusicBrainz tags
add_action('wp_ajax_scan_musicbrainz_tags', 'handle_musicbrainz_scan');

function handle_musicbrainz_scan()
{
  // Verify nonce
  $post_id = intval($_POST['post_id']);
  if (!wp_verify_nonce($_POST['nonce'], 'musicbrainz_scan_' . $post_id)) {
    wp_die('Security check failed');
  }

  // Verify permissions
  if (!current_user_can('edit_post', $post_id)) {
    wp_die('Insufficient permissions');
  }

  $artist = sanitize_text_field($_POST['artist']);
  $album = sanitize_text_field($_POST['album']);

  if (empty($artist) || empty($album)) {
    wp_send_json_error(array('message' => 'Artist and album name are required'));
  }

  try {
    $tags = query_musicbrainz_for_tags($artist, $album);
    wp_send_json_success($tags);
  } catch (Exception $e) {
    wp_send_json_error(array('message' => $e->getMessage()));
  }
}

// AJAX handler for applying selected tags
add_action('wp_ajax_apply_musicbrainz_tags', 'handle_apply_musicbrainz_tags');

function handle_apply_musicbrainz_tags()
{
  // Verify nonce
  $post_id = intval($_POST['post_id']);
  if (!wp_verify_nonce($_POST['nonce'], 'musicbrainz_apply_' . $post_id)) {
    wp_die('Security check failed');
  }

  // Verify permissions
  if (!current_user_can('edit_post', $post_id)) {
    wp_die('Insufficient permissions');
  }

  $tags = $_POST['tags'];

  try {
    // Apply genres
    if (!empty($tags['genres'])) {
      $genre_ids = array();
      foreach ($tags['genres'] as $genre_name) {
        $genre_name = sanitize_text_field($genre_name);
        $term = get_term_by('name', $genre_name, 'genre');
        if (!$term) {
          $result = wp_insert_term($genre_name, 'genre');
          if (!is_wp_error($result)) {
            $genre_ids[] = $result['term_id'];
          }
        } else {
          $genre_ids[] = $term->term_id;
        }
      }
      if (!empty($genre_ids)) {
        wp_set_post_terms($post_id, $genre_ids, 'genre', false);
      }
    }

    // Apply styles
    if (!empty($tags['styles'])) {
      $style_ids = array();
      foreach ($tags['styles'] as $style_name) {
        $style_name = sanitize_text_field($style_name);
        $term = get_term_by('name', $style_name, 'style');
        if (!$term) {
          $result = wp_insert_term($style_name, 'style');
          if (!is_wp_error($result)) {
            $style_ids[] = $result['term_id'];
          }
        } else {
          $style_ids[] = $term->term_id;
        }
      }
      if (!empty($style_ids)) {
        wp_set_post_terms($post_id, $style_ids, 'style', false);
      }
    }

    // Apply moods
    if (!empty($tags['moods'])) {
      $mood_ids = array();
      foreach ($tags['moods'] as $mood_name) {
        $mood_name = sanitize_text_field($mood_name);
        $term = get_term_by('name', $mood_name, 'mood');
        if (!$term) {
          $result = wp_insert_term($mood_name, 'mood');
          if (!is_wp_error($result)) {
            $mood_ids[] = $result['term_id'];
          }
        } else {
          $mood_ids[] = $term->term_id;
        }
      }
      if (!empty($mood_ids)) {
        wp_set_post_terms($post_id, $mood_ids, 'mood', false);
      }
    }

    // Apply labels (add after the moods section)
    if (!empty($tags['labels'])) {
      $label_ids = array();
      foreach ($tags['labels'] as $label_name) {
        $label_name = sanitize_text_field($label_name);
        $term = get_term_by('name', $label_name, 'labels');
        if (!$term) {
          $result = wp_insert_term($label_name, 'labels');
          if (!is_wp_error($result)) {
            $label_ids[] = $result['term_id'];
          }
        } else {
          $label_ids[] = $term->term_id;
        }
      }
      if (!empty($label_ids)) {
        wp_set_post_terms($post_id, $label_ids, 'labels', false);
      }
    }

    // Apply year
    if (!empty($tags['jaren'])) {
      foreach ($tags['jaren'] as $year_name) {
        $year_name = sanitize_text_field($year_name);
        $term = get_term_by('name', $year_name, 'jaren');
        if (!$term) {
          $result = wp_insert_term($year_name, 'jaren');
          if (!is_wp_error($result)) {
            wp_set_post_terms($post_id, array($result['term_id']), 'jaren', false);
          }
        } else {
          wp_set_post_terms($post_id, array($term->term_id), 'jaren', false);
        }
        break; // Only set one year
      }
    }

    // Apply cover art
    if (!empty($tags['cover'])) {
      $cover_url = sanitize_url($tags['cover'][0]);
      if ($cover_url) {
        set_cover_from_url($post_id, $cover_url);
      }
    }

    wp_send_json_success(array('message' => 'Tags applied successfully'));
  } catch (Exception $e) {
    wp_send_json_error(array('message' => $e->getMessage()));
  }
}

// Function to query MusicBrainz API for tags
function query_musicbrainz_for_tags($artist, $album)
{
  // First, search for the release
  $search_query = urlencode("artist:\"$artist\" AND release:\"$album\"");
  $search_url = "https://musicbrainz.org/ws/2/release/?query=$search_query&fmt=json&limit=1";

  $response = wp_remote_get($search_url, array(
    'timeout' => 10,
    'user-agent' => 'DeMaandagavond/1.0 (https://www.demaandagavond.nl)'
  ));

  if (is_wp_error($response)) {
    throw new Exception('Failed to connect to MusicBrainz: ' . $response->get_error_message());
  }

  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);

  if (!$data || !isset($data['releases']) || empty($data['releases'])) {
    throw new Exception('No matching releases found on MusicBrainz');
  }

  $release = $data['releases'][0];
  $release_id = $release['id'];

  // Get detailed release info with tags
  $detail_url = "https://musicbrainz.org/ws/2/release/$release_id?inc=tags+artist-credits+labels&fmt=json";

  $detail_response = wp_remote_get($detail_url, array(
    'timeout' => 10,
    'user-agent' => 'DeMaandagavond/1.0 (https://www.demaandagavond.nl)'
  ));

  if (is_wp_error($detail_response)) {
    throw new Exception('Failed to get release details from MusicBrainz');
  }

  $detail_body = wp_remote_retrieve_body($detail_response);
  $detail_data = json_decode($detail_body, true);

  // Also get artist tags
  $artist_tags = array();
  if (isset($detail_data['artist-credit'][0]['artist']['id'])) {
    $artist_id = $detail_data['artist-credit'][0]['artist']['id'];
    $artist_url = "https://musicbrainz.org/ws/2/artist/$artist_id?inc=tags&fmt=json";

    $artist_response = wp_remote_get($artist_url, array(
      'timeout' => 10,
      'user-agent' => 'DeMaandagavond/1.0 (https://www.demaandagavond.nl)'
    ));

    if (!is_wp_error($artist_response)) {
      $artist_body = wp_remote_retrieve_body($artist_response);
      $artist_data = json_decode($artist_body, true);
      if (isset($artist_data['tags'])) {
        $artist_tags = $artist_data['tags'];
      }
    }
  }

  // Combine release and artist tags
  $all_tags = array();
  if (isset($detail_data['tags'])) {
    $all_tags = array_merge($all_tags, $detail_data['tags']);
  }
  $all_tags = array_merge($all_tags, $artist_tags);

  // Categorize tags into genres, styles, and moods
  $genres = array();
  $styles = array();
  $moods = array();
  $labels = array();
  $release_year = null;

  // Define some common mood keywords
  $mood_keywords = array('happy', 'sad', 'energetic', 'calm', 'aggressive', 'melancholic', 'upbeat', 'dark', 'romantic', 'peaceful', 'intense', 'relaxed', 'nostalgic', 'dreamy', 'angry', 'cheerful', 'somber', 'mysterious', 'playful', 'atmospheric');

  // Define some style indicators (more specific sub-genres)
  $style_indicators = array('new wave', 'post-punk', 'alternative rock', 'indie rock', 'progressive rock', 'hard rock', 'soft rock', 'folk rock', 'psychedelic rock', 'blues rock', 'country rock', 'southern rock', 'classic rock', 'grunge', 'britpop', 'shoegaze', 'dream pop', 'lo-fi', 'ambient', 'experimental', 'minimalist');

  foreach ($all_tags as $tag) {
    $tag_name = strtolower($tag['name']);
    $tag_display = $tag['name'];

    // Check if it's a mood
    $is_mood = false;
    foreach ($mood_keywords as $mood_keyword) {
      if (strpos($tag_name, $mood_keyword) !== false) {
        $moods[] = $tag_display;
        $is_mood = true;
        break;
      }
    }

    if (!$is_mood) {
      // Check if it's a style (more specific)
      $is_style = false;
      foreach ($style_indicators as $style_indicator) {
        if (strpos($tag_name, $style_indicator) !== false) {
          $styles[] = $tag_display;
          $is_style = true;
          break;
        }
      }

      // If not a style or mood, it's likely a genre
      if (!$is_style) {
        // Filter out very common/generic tags
        $exclude_tags = array('seen live', 'favorite', 'albums i own', 'under 2000 listeners', 'guitar', 'drums', 'bass', 'vocals', 'instrumental');
        if (!in_array($tag_name, $exclude_tags)) {
          $genres[] = $tag_display;
        }
      }
    }
  }

  // Get Label
  if (isset($detail_data['label-info'])) {
    foreach ($detail_data['label-info'] as $label_info) {
      if (isset($label_info['label']['name'])) {
        $labels[] = $label_info['label']['name'];
      }
    }
  }

  // Get Year
  if (isset($detail_data['date'])) {
    $release_year = substr($detail_data['date'], 0, 4); // Extract year from date
  }

  // Get cover art URL - try multiple approaches
  $cover_url = null;

  // Try the front cover first
  $cover_api_url = "https://coverartarchive.org/release/$release_id/front";
  $cover_response = wp_remote_get($cover_api_url, array(
    'timeout' => 10,
    'redirection' => 5
  ));

  if (!is_wp_error($cover_response)) {
    $response_code = wp_remote_retrieve_response_code($cover_response);
    if ($response_code === 200) {
      $cover_url = $cover_api_url;
    } elseif ($response_code === 307 || $response_code === 302) {
      // Follow redirect manually
      $location = wp_remote_retrieve_header($cover_response, 'location');
      if ($location) {
        $cover_url = $location;
      }
    }
  }

  // If front cover fails, try the general cover art endpoint
  if (!$cover_url) {
    $cover_api_url = "https://coverartarchive.org/release/$release_id";
    $cover_response = wp_remote_get($cover_api_url, array(
      'timeout' => 10
    ));

    if (!is_wp_error($cover_response) && wp_remote_retrieve_response_code($cover_response) === 200) {
      $cover_data = json_decode(wp_remote_retrieve_body($cover_response), true);
      if (isset($cover_data['images'][0]['image'])) {
        $cover_url = $cover_data['images'][0]['image'];
      }
    }
  }

  // Remove duplicates and limit results
  $genres = array_unique($genres);
  $styles = array_unique($styles);
  $moods = array_unique($moods);

  // Limit to reasonable number of suggestions
  $genres = array_slice($genres, 0, 5);
  $styles = array_slice($styles, 0, 10);
  $moods = array_slice($moods, 0, 10);

  return array(
    'genres' => array_values($genres),
    'styles' => array_values($styles),
    'moods' => array_values($moods),
    'labels' => array_unique($labels),
    'year' => $release_year,
    'cover_url' => $cover_url
  );
}

function set_cover_from_url($post_id, $image_url)
{
  $response = wp_remote_get($image_url);

  if (is_array($response)) {
    $image_data = wp_remote_retrieve_body($response);
    $upload = wp_upload_bits(basename($image_url), null, $image_data);

    if (!$upload['error']) {
      $file_path = $upload['file'];
      $file_name = basename($file_path);
      $file_type = wp_check_filetype($file_name, null);

      // Prepare the attachment post data
      $attachment = [
        'post_mime_type' => $file_type['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $file_name),
        'post_content'   => '',
        'post_status'    => 'inherit'
      ];

      // Insert the attachment
      $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

      // Include the image.php file for wp_generate_attachment_metadata()
      require_once(ABSPATH . 'wp-admin/includes/image.php');

      $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

      // Update attachment metadata
      wp_update_attachment_metadata($attach_id, $attach_data);

      // Set as featured image
      set_post_thumbnail($post_id, $attach_id);
    }
  }
}
