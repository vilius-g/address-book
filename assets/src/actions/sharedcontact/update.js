import {SubmissionError} from 'redux-form';
import {extractHubURL, fetch, mercureSubscribe as subscribe, normalize} from '../../utils/dataAccess';
import {success as createSuccess} from './create';
import {error, loading} from './delete';

export function retrieveError(retrieveError) {
    return {type: 'SHAREDCONTACT_UPDATE_RETRIEVE_ERROR', retrieveError};
}

export function retrieveLoading(retrieveLoading) {
    return {type: 'SHAREDCONTACT_UPDATE_RETRIEVE_LOADING', retrieveLoading};
}

export function retrieveSuccess(retrieved) {
    return {type: 'SHAREDCONTACT_UPDATE_RETRIEVE_SUCCESS', retrieved};
}

export function retrieve(id) {
    return dispatch => {
        dispatch(retrieveLoading(true));

        return fetch(id)
            .then(response =>
                response
                    .json()
                    .then(retrieved => ({retrieved, hubURL: extractHubURL(response)}))
            )
            .then(({retrieved, hubURL}) => {
                retrieved = normalize(retrieved);

                dispatch(retrieveLoading(false));
                dispatch(retrieveSuccess(retrieved));

                if (hubURL) dispatch(mercureSubscribe(hubURL, retrieved['@id']));
            })
            .catch(e => {
                dispatch(retrieveLoading(false));
                dispatch(retrieveError(e.message));
            });
    };
}

export function updateError(updateError) {
    return {type: 'SHAREDCONTACT_UPDATE_UPDATE_ERROR', updateError};
}

export function updateLoading(updateLoading) {
    return {type: 'SHAREDCONTACT_UPDATE_UPDATE_LOADING', updateLoading};
}

export function updateSuccess(updated) {
    return {type: 'SHAREDCONTACT_UPDATE_UPDATE_SUCCESS', updated};
}

export function update(item, values) {
    return dispatch => {
        dispatch(updateError(null));
        dispatch(createSuccess(null));
        dispatch(updateLoading(true));

        return fetch(item['@id'], {
            method: 'PUT',
            headers: new Headers({'Content-Type': 'application/ld+json'}),
            body: JSON.stringify(values)
        })
            .then(response =>
                response
                    .json()
                    .then(retrieved => ({retrieved, hubURL: extractHubURL(response)}))
            )
            .then(({retrieved, hubURL}) => {
                retrieved = normalize(retrieved);

                dispatch(updateLoading(false));
                dispatch(updateSuccess(retrieved));

                if (hubURL) dispatch(mercureSubscribe(hubURL, retrieved['@id']));
            })
            .catch(e => {
                dispatch(updateLoading(false));

                if (e instanceof SubmissionError) {
                    dispatch(updateError(e.errors._error));
                    throw e;
                }

                dispatch(updateError(e.message));
            });
    };
}

export function reset(eventSource) {
    return dispatch => {
        if (eventSource) eventSource.close();

        dispatch({type: 'SHAREDCONTACT_UPDATE_RESET'});
        dispatch(error(null));
        dispatch(loading(false));
        dispatch(createSuccess(null));
    };
}

export function mercureSubscribe(hubURL, topic) {
    return dispatch => {
        const eventSource = subscribe(hubURL, [topic]);
        dispatch(mercureOpen(eventSource));
        eventSource.addEventListener('message', event =>
            dispatch(mercureMessage(normalize(JSON.parse(event.data))))
        );
    };
}

export function mercureOpen(eventSource) {
    return {type: 'SHAREDCONTACT_UPDATE_MERCURE_OPEN', eventSource};
}

export function mercureMessage(retrieved) {
    return dispatch => {
        if (1 === Object.keys(retrieved).length) {
            dispatch({type: 'SHAREDCONTACT_UPDATE_MERCURE_DELETED', retrieved});
            return;
        }

        dispatch({type: 'SHAREDCONTACT_UPDATE_MERCURE_MESSAGE', retrieved});
    };
}