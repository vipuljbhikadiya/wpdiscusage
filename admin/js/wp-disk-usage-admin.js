window.addEventListener( 'DOMContentLoaded', function() {
	const chunk_size = 10;
	let chunk_data   = [];

	// Genrate statisctics
	const generateBtn = document.querySelector( '.generate-statistics' );

	generateBtn.addEventListener( 'click', async function() {
		generateBtn.disabled = true;
		const statistics = await generate_disc_usage_statistic();
		generateBtn.disabled = false;

		//storeStatistics( chunk_data );

		const fileListWrap    = document.querySelector( '.wpdu-file-list-wrapper' );
		const instructionText = document.querySelector( '.generate-statics-block.no-data' );
		fileListWrap.classList.remove( 'hide' );
		instructionText.classList.add( 'hide' );
	});

	const generate_disc_usage_statistic = async function( directory = false, offset = false, chunk_size = false, defaultRender = true ) {
		const params = new URLSearchParams();

		params.append( 'action', 'wpdu_generate_statistics' );

		if ( directory ) {
			params.append( 'directory', directory );
		}

		if ( offset ) {
			params.append( 'offset', offset );
		}

		if ( chunk_size ) {
			params.append( 'chunk_size', chunk_size );
		}

		const response = await fetch( wpdu_params.ajaxurl,
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: params
			}
		);

		const jsonData = await response.json();

		if ( jsonData.success ) {
			const items           = jsonData.data.items;
			const current_dir     = jsonData.data.directory;
			const curr_offset     = jsonData.data.offset;
			const curr_chunk_size = jsonData.data.chunk_size;

			chunk_data = [...chunk_data, ...items];

			//chunk_data.push( items );
			if ( items.length >= chunk_size ) {
				await generate_disc_usage_statistic( current_dir, curr_offset, curr_chunk_size, defaultRender );
			} else {
				if ( defaultRender ) {
					render_file_list( chunk_data );
				}
			}
		}

		return jsonData;
	}

	// Render directories
	const render_file_list = function render_file_list( dirItems ) {
		const { directoryItems, directories } = sortingDirectory( dirItems );

		const directoryHtml      = generateDirectoryHTML( directories );
		const directoryInnerHtml = generateDirectoryInnerHTML( directoryItems );

		const directoryContainer      = document.querySelector( '.wpdu-directories' );
		const directoryInnerContainer = document.querySelector( '.wpdu-files' );

		directoryContainer.innerHTML      = directoryHtml;
		directoryInnerContainer.innerHTML = directoryInnerHtml;

		directoryContainer.addEventListener( 'click', handleItemClick );
		directoryInnerContainer.addEventListener( 'click', deleteFile );
	};

	// Handle click event using event delegation
	async function handleItemClick( event ) {
		if ( event.target.classList.contains( 'directory' ) ) {
			const target = event.target;
			const path   = target.dataset.path;

			chunk_data = [];

			await generate_disc_usage_statistic( path, false, false, false );

			const { directoryItems, directories } = sortingDirectory( chunk_data );

			const directoryHtml      = generateDirectoryHTML( directories );
			const directoryInnerHtml = generateDirectoryInnerHTML( directoryItems );

			const directoryInnerContainer = document.querySelector( '.wpdu-files' );
			directoryInnerContainer.innerHTML = directoryInnerHtml;

			// Append HTML content using insertAdjacentHTML
			target.insertAdjacentHTML( 'beforeend', directoryHtml );


		}
	}

	async function deleteFile( event ) {
		event.preventDefault();
		if ( event.target.classList.contains( 'file-delete' ) ) {
			const target = event.target;
			const path   = target.dataset.path;

			const conf = confirm( 'Are you sure you want to delete this item?' ) ? true : false;

			// Remove file for like soft delete
			if ( conf ) {
				target.closest( 'tr' ).remove();
			}
		}
	}

	// Function to generate the directory structure HTML
	function generateDirectoryHTML( directories ) {
		let html = '<ul>';
	
		directories.forEach( item => {
			html += '<li class="directory" data-path="' + item.path + '"><span class="dashicons dashicons-open-folder"></span> ' + item.name + '</li>';
		});
	
		html += '</ul>';
	
		return html;
	}

	// Function to generate the directory structure HTML
	function generateDirectoryInnerHTML( directories ) {
		let html = '';

		html += '<table class="widefat">';
			html += '<thead>\
				<tr>\
					<td>Name</td>\
					<td>Subtree Percentage</td>\
					<td>Size</td>\
					<td>Action</td>\
				</tr>\
			</thead>';

		if ( directories.length ) {
			html += '<tbody>';

			directories.forEach( item => {
				html += '<tr>' + 
				'<td><span class="' + ( ( 'directory' === item.type ) ? 'dashicons dashicons-open-folder' : 'dashicons dashicons-media-default' ) + '"></span> ' + item.name + '</td>\
				<td><div class="flex"><div class="wpdu-progress"><div class="wpdu-progress-bar" style="width: ' + item.percentate + '%"></div></div><span class="file-percentage">' + item.percentate + '%</span></div>\
				</td>\
				<td>' + item.format_size + '</td>\
				<td><a href="#" title="Delete" class="file-delete" data-path="' + item.path + '"><span class="dashicons dashicons-trash"></span></a></td>\
				</tr>';
			});

			html += '</tbody>';
		} else {
			html += '<tr><td colspan="4" aling="center">No files.</td><\tr>';
		}

		html += '</table>';

		return html;
	}

	// Function to sort directory
	function sortingDirectory( directoryItems ) {
		 directoryItems.sort((a, b) => {
			if (a.type !== b.type) {
			  return a.type === 'directory' ? -1 : 1;
			} else {
			  return a.name.localeCompare(b.name);
			}
		});

		// Separate the sorted items into files and directories
		let directories = directoryItems.filter( item => item.type === 'directory' );

		return { 'directoryItems': directoryItems, 'directories': directories };
	}
});
