import { useMutation, useQueryClient } from '@tanstack/react-query';
import createData from '@helper/createData.js';

const useCreateMutation = ( path ) => {
	const queryClient = useQueryClient();
	return useMutation( ( data ) => createData( path, data ) );
};

export default useCreateMutation;

// , {
// 	onMutate: async ( data ) => {
// 		await queryClient.cancelQueries( [ queryKey ] );
// 		const previousValue = queryClient.getQueryData( [ queryKey ] );
// 		// queryClient.setQueryData([ queryKey ], (old) => {...old, old[]});
// 		typeof callback === 'function' && callback( previousValue, data );
// 		return previousValue;
// 	},
// 	onError: ( error, __, previousValue ) => {
// 		queryClient.setQueryData( [ queryKey ], previousValue );
// 	},
// 	onSettled: () => queryClient.invalidateQueries( [ queryKey ] ),
// }
