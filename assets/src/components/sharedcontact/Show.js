import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Link, Redirect} from 'react-router-dom';
import PropTypes from 'prop-types';
import {reset, retrieve} from '../../actions/sharedcontact/show';
import {del} from '../../actions/sharedcontact/delete';

class Show extends Component {
    static propTypes = {
        retrieved: PropTypes.object,
        loading: PropTypes.bool.isRequired,
        error: PropTypes.string,
        eventSource: PropTypes.instanceOf(EventSource),
        retrieve: PropTypes.func.isRequired,
        reset: PropTypes.func.isRequired,
        deleteError: PropTypes.string,
        deleteLoading: PropTypes.bool.isRequired,
        deleted: PropTypes.object,
        del: PropTypes.func.isRequired
    };

    componentDidMount() {
        this.props.retrieve(decodeURIComponent(this.props.match.params.id));
    }

    componentWillUnmount() {
        this.props.reset(this.props.eventSource);
    }

    del = () => {
        if (window.confirm('Are you sure you want to delete this item?'))
            this.props.del(this.props.retrieved);
    };

    render() {
        if (this.props.deleted) return <Redirect to=".."/>;

        const item = this.props.retrieved;

        return (
            <div>
                <h1>Show {item && item['@id']}</h1>

                {this.props.loading && (
                    <div className="alert alert-info" role="status">
                        Loading...
                    </div>
                )}
                {this.props.error && (
                    <div className="alert alert-danger" role="alert">
                        <span className="fa fa-exclamation-triangle" aria-hidden="true"/>{' '}
                        {this.props.error}
                    </div>
                )}
                {this.props.deleteError && (
                    <div className="alert alert-danger" role="alert">
                        <span className="fa fa-exclamation-triangle" aria-hidden="true"/>{' '}
                        {this.props.deleteError}
                    </div>
                )}

                {item && (
                    <table className="table table-responsive table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">contact</th>
                            <td>{this.renderLinks('contacts', item['contact'])}</td>
                        </tr>
                        <tr>
                            <th scope="row">owner</th>
                            <td>{this.renderLinks('users', item['owner'])}</td>
                        </tr>
                        <tr>
                            <th scope="row">sharedWith</th>
                            <td>{this.renderLinks('users', item['sharedWith'])}</td>
                        </tr>
                        </tbody>
                    </table>
                )}
                <Link to=".." className="btn btn-primary">
                    Back to list
                </Link>
                {item && (
                    <Link to={`/shared_contacts/edit/${encodeURIComponent(item['@id'])}`}>
                        <button className="btn btn-warning">Edit</button>
                    </Link>
                )}
                <button onClick={this.del} className="btn btn-danger">
                    Delete
                </button>
            </div>
        );
    }

    renderLinks = (type, items) => {
        if (Array.isArray(items)) {
            return items.map((item, i) => (
                <div key={i}>{this.renderLinks(type, item)}</div>
            ));
        }

        return (
            <Link to={`../../${type}/show/${encodeURIComponent(items)}`}>
                {items}
            </Link>
        );
    };
}

const mapStateToProps = state => ({
    retrieved: state.sharedcontact.show.retrieved,
    error: state.sharedcontact.show.error,
    loading: state.sharedcontact.show.loading,
    eventSource: state.sharedcontact.show.eventSource,
    deleteError: state.sharedcontact.del.error,
    deleteLoading: state.sharedcontact.del.loading,
    deleted: state.sharedcontact.del.deleted
});

const mapDispatchToProps = dispatch => ({
    retrieve: id => dispatch(retrieve(id)),
    del: item => dispatch(del(item)),
    reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(Show);
