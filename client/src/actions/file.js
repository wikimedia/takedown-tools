export function addMultiple( files ) {
	return {
		type: 'FILE_ADD_MULTIPLE',
		files: files
	};
}

export function add( file ) {
	return {
		type: 'FILE_ADD',
		file: file
	};
}

export function update( file ) {
	return {
		type: 'FILE_UPDATE',
		file: file
	};
}

export function remove( file ) {
	return {
		type: 'FILE_REMOVE',
		file: file
	};
}

export function deleteFile( file ) {
	return {
		type: 'FILE_DELETE',
		file: file
	};
}

export function swap( oldFile, newFile ) {
	return {
		type: 'FILE_SWAP',
		oldFile: oldFile,
		newFile: newFile
	};
}
