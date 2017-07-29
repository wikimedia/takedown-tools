import React from 'react';
import PropTypes from 'prop-types';
import { Set } from 'immutable';
import Select from 'react-select';
import 'react-select/dist/react-select.css';
import { TakedownCreateDmcaContainer } from './dmca/dmca.container';
import { TakedownCreateCpContainer } from './cp/cp.container';
import { Submit } from 'app/components/fields/submit';
import { FormError } from 'app/components/fields/form-error';
import { FormGroup } from 'app/components/fields/form-group';
import { SelectPages } from 'app/components/fields/select-pages';
import { SelectUsers } from 'app/components/fields/select-users';
import { MetadataField } from 'app/components/fields/metadata';
import { Takedown } from 'app/entities/takedown/takedown';
import { Site } from 'app/entities/site';
import { User } from 'app/entities/user';
import { removeErrors } from 'app/utils';

export class TakedownCreate extends React.Component {

	componentWillMount() {
		this.props.fetchSites();
		this.componentWillReceiveProps( this.props );
	}

	componentWillReceiveProps( nextProps ) {
		let takedown;

		if ( nextProps.reporter.id && !nextProps.takedown.reporterId ) {
			takedown = nextProps.takedown.set( 'reporterId', nextProps.reporter.id );
			nextProps.updateTakedown( takedown );
		}
	}

	removeErrors( takedown, propertyPath ) {
		let violations;

		if ( takedown.error && propertyPath ) {
			violations = takedown.error.constraintViolations.filter( ( violation ) => {
				return violation.propertyPath !== propertyPath;
			} );

			takedown = takedown.setIn( [ 'error', 'constraintViolations' ], violations );
		}

		return takedown;
	}

	updateField( fieldName, value ) {
		let takedown = this.props.takedown
			.set( fieldName, value )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, fieldName );

		this.props.updateTakedown( takedown );
	}

	updateInvolved( involved ) {
		let takedown = this.props.takedown
			.set( 'involvedIds', involved.map( ( user ) => {
				return user.id;
			} ) )
			.set( 'status', 'dirty' );

		takedown = removeErrors( takedown, 'invovled' );

		this.props.addUsers( involved );
		this.props.updateTakedown( takedown );
	}

	onSubmit( e ) {
		e.preventDefault();
		this.props.saveTakedown();
	}

	render() {
		let disabled = this.props.takedown.status === 'saving',
			dmcaButtonClass = 'btn btn-secondary',
			cpButtonClass = 'btn btn-secondary',
			takedownTypeForm,
			metaDataField;

		switch ( this.props.takedown.type ) {
			case 'dmca':
				dmcaButtonClass = dmcaButtonClass + ' active';
				takedownTypeForm = (
					<TakedownCreateDmcaContainer disabled={disabled} />
				);
				break;

			case 'cp':
				cpButtonClass = cpButtonClass + ' active';
				takedownTypeForm = (
					<TakedownCreateCpContainer disabled={disabled} />
				);
				break;
		}

		if ( this.props.takedown.type ) {
			metaDataField = (
				<FormGroup path="metadataIds" error={this.props.takedown.error} render={() => (
					<div>
						<label className="form-control-label" htmlFor="metadataIds">Metadata</label> <small id="passwordHelpInline" className="text-muted">check all that are true</small>
						<MetadataField type={this.props.takedown.type} value={this.props.takedown.metadataIds} onChange={( value ) => this.updateField( 'metadataIds', value )} />
					</div>
				)} />
			);
		}

		return (
			<div className="row">
				<div className="col">
					<h2>Create Takedown</h2>
					<form onSubmit={this.onSubmit.bind( this )}>
						<FormGroup path="siteId" error={this.props.takedown.error} render={ () => (
							<div>
								<label className="form-control-label" htmlFor="siteId">Site</label>
								<Select name="siteId" disabled={disabled} options={this.props.siteOptions} value={this.props.takedown.siteId} onChange={( data ) => this.updateField( 'siteId', data ? data.value : undefined )} />
							</div>
						) } />
						<FormGroup path="involvedIds" error={this.props.takedown.error} render={ () => (
							<div>
								<label className="form-control-label" htmlFor="involvedIds">Involved Users</label>
								<SelectUsers disabled={disabled} name="involvedIds" multi={true} value={this.props.involved} users={ this.props.users.toArray() } onChange={this.updateInvolved.bind( this )} />
							</div>
						) } />
						<FormGroup path="pageIds" error={this.props.takedown.error} render={ () => (
							<div>
								<label className="form-control-label" htmlFor="pageIds">Affected Pages</label>
								<SelectPages disabled={disabled} site={this.props.site} name="pageIds" value={this.props.takedown.pageIds} onChange={ ( pageIds ) => this.updateField( 'pageIds', pageIds ) } />
							</div>
						) } />
						<div className="form-group">
							<label className="form-control-label">Type</label>
							<div className="row">
								<div className="col btn-group">
									<button type="button" disabled={disabled} style={ { zIndex: 0 } } className={dmcaButtonClass} onClick={() => this.updateField( 'type', 'dmca' )}>DMCA</button>
									<button type="button" disabled={disabled} style={ { zIndex: 0 } } className={cpButtonClass} onClick={() => this.updateField( 'type', 'cp' )}>Child Protection</button>
								</div>
							</div>
						</div>
						{metaDataField}
						{takedownTypeForm}
						<div className="form-group row align-items-center">
							<div className="col-11">
								<FormError error={this.props.takedown.error} />
							</div>
							<div className="col-1 text-right">
								<Submit status={this.props.takedown.status} value="Save" />
							</div>
						</div>
					</form>
				</div>
			</div>
		);
	}
}

TakedownCreate.propTypes = {
	fetchSites: PropTypes.func.isRequired,
	takedown: PropTypes.instanceOf( Takedown ).isRequired,
	users: PropTypes.instanceOf( Set ).isRequired,
	site: PropTypes.instanceOf( Site ),
	involved: PropTypes.arrayOf( PropTypes.instanceOf( User ) ).isRequired,
	siteOptions: PropTypes.arrayOf( PropTypes.shape( {
		label: PropTypes.string,
		value: PropTypes.string
	} ) ),
	reporter: PropTypes.instanceOf( User ).isRequired,
	updateTakedown: PropTypes.func.isRequired,
	saveTakedown: PropTypes.func.isRequired,
	addUsers: PropTypes.func.isRequired
};
